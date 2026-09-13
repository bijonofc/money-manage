<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $title = 'Money Manage Test Email',
        public string $bodyText = 'This is a test email from Money Manage to confirm your SMTP configuration is working properly.'
    ) {
        $this->onQueue('default');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Money Manage - SMTP Test Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.test',
            with: [
                'title' => $this->title,
                'bodyText' => $this->bodyText,
                'sentAt' => now()->toDateTimeString(),
            ]
        );
    }
}
