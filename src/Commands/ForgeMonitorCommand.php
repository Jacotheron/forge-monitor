<?php

namespace Jacotheron\ForgeMonitor\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Process\Process;
use Jacotheron\ForgeMonitor\Mail\ForgeNotification;

class ForgeMonitorCommand extends Command
{
    public $signature = 'forge-monitor';

    public $description = 'Monitor sites in the laravel forge server - disk usage';

    public function handle(): int
    {
        //result arrays
        $disk = '';
        $results = [];
        $database_sizes = [];

        if(is_array(config('forge-monitor.disc_commands')) && count(config('forge-monitor.disc_commands')) > 0){
            $disk_process = new Process(config('forge-monitor.disc_commands'));
            $disk_process->setTimeout(config('forge-monitor.disk_command_timeout'));
            $disk_process->run();
            if($disk_process->isSuccessful()){
                $disk = $disk_process->getOutput();
                $this->info(config('forge-monitor.strings.disk_results'));
                $this->info($disk);
            }else{
                $this->error('Failed to get disk usage');
            }
        }

        if($location = config('forge-monitor.projects_location')){
            $project_commands = config('forge-monitor.project_commands');
            if(config('forge-monitor.project_commands_on_projects_location') || config('forge-monitor.project_commands_only_on_projects_location')){
                //handle the all projects option
                $this_commands = array_merge($project_commands, [$location]);
                $process = new Process($this_commands);
                $process->setTimeout(config('forge-monitor.project_commands_timeout'));
                $process->run();
                if($process->isSuccessful()){
                    $results[] = $process->getOutput().' '.config('forge-monitor.strings.all_projects')."\n\n";
                }else{
                    $this->error('Failed to get all project usage');
                }
            }
            if(!config('forge-monitor.project_commands_only_on_projects_location')){
                $scan = scandir($location);
                foreach ($scan as $file){
                    if (Str::startsWith($file, '.')) {
                        continue;
                    }
                    if (! is_dir($location.'/'.$file)) {
                        continue;
                    }
                    $this_commands = array_merge($project_commands, [$location.'/'.$file]);
                    $process = new Process($this_commands);
                    $process->setTimeout(config('forge-monitor.project_commands_timeout'));
                    $process->run();
                    if($process->isSuccessful()){
                        $results[] = $process->getOutput();
                    }else{
                        $this->error('Failed to get project usage: '. $file);
                    }
                }
            }

            $this->info('');
            $this->info(config('forge-monitor.strings.scan_results'));
            foreach ($results as $result) {
                $this->info($result);
            }
        }

        if($connection = config('forge-monitor.db_driver')){
            $databases_sizes = DB::connection($connection)
                ->table('TABLES')
                ->select(DB::raw('TABLE_SCHEMA as `database`, SUM(DATA_LENGTH + INDEX_LENGTH) as `size`'))
                ->groupBy('TABLE_SCHEMA')
                ->orderBy('size', 'desc')
                ->get();

            $this->info('');
            $this->info(config('forge-monitor.strings.db_results'));

            $db_total_size = 0;

            foreach ($databases_sizes as $object) {
                $database_sizes[] = [$object->database, $object->size];
                $db_total_size += $object->size;
            }

            $database_sizes[] = ['Total', $db_total_size];

            $this->table(['Database', 'Size'], $database_sizes);
        }

        if(config('forge-monitor.email.to_address')){
            Mail::to(config('forge-monitor.email.to_address'))
                ->send(new ForgeNotification($results, $disk, $database_sizes));
        }


        $this->comment('');
        $this->comment('All done');

        return self::SUCCESS;
    }
}
