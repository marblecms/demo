<?php

namespace MarbleCms\Newsletter\Mail;

use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use MarbleCms\Newsletter\Models\Campaign;

class CampaignMail extends Mailable
{
    public function __construct(
        public readonly Campaign $campaign,
        public readonly string   $processedBody,
    ) {
    }

    public function envelope(): Envelope
    {
        $envelope = new Envelope(
            from: new \Illuminate\Mail\Mailables\Address(
                config('newsletter.from_email'),
                config('newsletter.from_name'),
            ),
            subject: $this->campaign->subject,
        );

        if ($this->campaign->reply_to) {
            $envelope = new Envelope(
                from: new \Illuminate\Mail\Mailables\Address(
                    config('newsletter.from_email'),
                    config('newsletter.from_name'),
                ),
                replyTo: [new \Illuminate\Mail\Mailables\Address($this->campaign->reply_to)],
                subject: $this->campaign->subject,
            );
        }

        return $envelope;
    }

    public function content(): Content
    {
        return new Content(
            view: 'newsletter::emails.campaign',
        );
    }
}
