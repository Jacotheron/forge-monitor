<?php

namespace Jacotheron\ForgeMonitor\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgeNotification extends Mailable implements ShouldQueue
{
    use SerializesModels;

    public function __construct(public array $command_output, public string $disk_details, public array $database_sizes){}

    public function build(): self
    {
        $subject = str_replace(
            '{date}',
            \Illuminate\Support\now()->format(config('forge-monitor.notification.date_format')),
            config('forge-monitor.email.subject')
        );

        return $this
            ->subject($subject)
            ->markdown('forge-monitor::mail.forge-notification');
    }

}