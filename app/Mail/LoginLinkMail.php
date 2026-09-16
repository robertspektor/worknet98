<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LoginLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public readonly string $url,
        public readonly int $lifetimeMinutes,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('mail.login_link.subject'));
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.login-link');
    }
}
