<?php

namespace App\Modules\Main\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CredentialSendMail extends Mailable
{
    use Queueable, SerializesModels;

    private $user;
    private $credentials;

    /**
     * Create a new message instance.
     */
    public function __construct($user, $credentials = [])
    {
        $this->user = $user;
        $this->credentials = $credentials;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: config('common.cms.short_title') . ' - Your Account Credentials',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'Main::mails.credential',
            with: [
                'user'     => $this->user ?? '',
                'email'    => $this->credentials['email'] ?? '',
                'password' => $this->credentials['password'] ?? '',
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
