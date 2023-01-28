<?php

namespace Jacotheron\ForgeMonitor\Console;

use Illuminate\Support\Facades\Mail;
use Jacotheron\ForgeMonitor\Mail\ForgeMonitorMail;
use Throwable;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;

class MonitorCommand extends Command
{
    protected $signature = 'monitor-forge-sites';

    protected $description = 'Monitor sites in the laravel forge server - disk usage';

    public function handle(): int
    {
        $project_results = [];
        $db_results = [];
        $disk_result = '';
        $self_project_result = '';

        $has_started_output = false;

        if(config('forge-monitor.checks_to_run.global_disk_usage')){
            $disk_result = $this->disk_result();

            $this->info('Disk Results:');
            $this->info($disk_result);

            $has_started_output = true;
        }

        if(config('forge-monitor.checks_to_run.per_project_disk_usage')){
            $project_results = $this->project_list_result(config('forge-monitor.sites_locations'));

            if($has_started_output){
                $this->info('');
            }

            $this->info('Project Scan Results:');
            foreach($project_results as $result){
                $this->info($result);
            }

            $has_started_output = true;
        }

        if(config('forge-monitor.checks_to_run.self_disk_usage')){
            $self_project_result = $this->self_project_result();

            if($has_started_output){
                $this->info('');
            }

            $this->info('Self Scan Results:');
            $this->info($self_project_result);

            $has_started_output = true;
        }

        if(config('forge-monitor.checks_to_run.database_disk_usage')){
            $db_results = $this->db_results(config('forge-monitor.database_connection'));

            if($has_started_output){
                $this->info('');
            }

            $this->info('Database Results:');
            $this->table(['Database', 'Size'], $db_results);

            $has_started_output = true;
        }

        if(config('forge-monitor.email.enabled') && !empty(config('forge-monitor.email.address'))){
            $this->info('');
            $this->info('Sending Email');
            $this->send_email(config('forge-monitor.email.address'), config('forge-monitor.email.subject'), $disk_result, $project_results, $self_project_result, $db_results);
            $this->info('Email Sent');
        }

        $this->info('');
        $this->info('Done');
        return 0;
    }

    private function disk_result(): string
    {
        try {
            $disk_process = new Process(['df', '-h', '/']);
            $disk_process->run();
            return $disk_process->getOutput();
        } catch (Throwable){}
        return '';
    }

    private function project_list_result($locations): array
    {
        $results = [];

        foreach($locations as $location){
            try {
                $process = new Process(['du', '-hs', $location]);
                $process->run();
                $results[] = $process->getOutput()." (All Projects - ".$location.")\n\n";
            }catch (Throwable){
                continue; //if we perhaps can't run this command, the rest will probably also fail
            }
            $scan = scandir($location);
            foreach($scan as $file){
                if(!is_dir($location.'/'.$file) || Str::startsWith($file, '.')){
                    continue;
                }

                try {
                    $process = new Process(['du', '-hs', $location.'/'.$file]);
                    $process->run();
                    $results[] = $process->getOutput();
                }catch (Throwable){}
            }
        }

        return $results;
    }

    private function self_project_result():string
    {
        $location = base_path();
        $result = '';
        try {
            $process = new Process(['du', '-hs', $location]);
            $process->run();
            $result = $process->getOutput();
        }catch (Throwable){}
        return $result;
    }

    private function db_results($connection):array{
        $database_sizes = [];
        $db_total_size = 0;

        $databases_sizes = DB::connection($connection)
            ->table('TABLES')
            ->select(DB::raw('TABLE_SCHEMA as `database`, SUM(DATA_LENGTH + INDEX_LENGTH) as `size`'))
            ->groupBy('TABLE_SCHEMA')
            ->orderBy('size', 'desc')
            ->get();

        foreach($databases_sizes as $object){
            $database_sizes[] = [$object->database, $object->size];
            $db_total_size += $object->size;
        }
        $database_sizes[] = ['Total', $db_total_size];

        return $database_sizes;
    }

    private function send_email($email_address, $email_subject, $disk_result, $project_results, $self_project_result, $db_results): void
    {
        Mail::to($email_address)->send(new ForgeMonitorMail($email_address, $email_subject, $disk_result, $project_results, $self_project_result, $db_results));
    }
}