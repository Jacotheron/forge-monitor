<?php

namespace Jacotheron\ForgeMonitor\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Envelope;

class ForgeMonitorMail extends Mailable implements ShouldQueue
{
    use SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $email_address, public $email_subject, public $disk_result, public $project_results, public $self_project_result, public $db_results)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            to: new Address($this->email_address),
            subject: $this->subject
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'forge-monitor::mail.forge-notification'
        );
    }
}