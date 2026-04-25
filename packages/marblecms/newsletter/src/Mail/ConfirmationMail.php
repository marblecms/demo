<?php

namespace MarbleCms\Newsletter\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use MarbleCms\Newsletter\Models\Subscriber;

class ConfirmationMail extends Mailable
{
    public string $confirmationUrl;

    public function __construct(public readonly Subscriber $subscriber)
    {
        $this->confirmationUrl = route('newsletter.confirm', $subscriber->confirmation_token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('newsletter.from_email'),
                config('newsletter.from_name'),
            ),
            subject: config('newsletter.confirmation_subject', 'Please confirm your subscription'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: config('newsletter.confirmation_view', 'newsletter::emails.confirmation'),
        );
    }
}
