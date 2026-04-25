<?php

namespace MarbleCms\Newsletter\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use MarbleCms\Newsletter\Events\CampaignSent;
use MarbleCms\Newsletter\Mail\CampaignMail;
use MarbleCms\Newsletter\Models\Campaign;
use MarbleCms\Newsletter\Models\CampaignSend;
use MarbleCms\Newsletter\Models\Subscriber;

class MailingService
{
    /**
     * Send a campaign to all eligible subscribers.
     * Creates CampaignSend rows, injects tracking pixel + unsubscribe link, then sends.
     *
     * @return int Number of emails successfully sent
     */
    public function send(Campaign $campaign): int
    {
        if ($campaign->status === 'sent') {
            return 0;
        }

        // Resolve subscriber set
        if ($campaign->list_id) {
            $subscribers = Subscriber::confirmed()
                ->whereHas('lists', fn($q) => $q->where('newsletter_lists.id', $campaign->list_id))
                ->get();
        } else {
            $subscribers = Subscriber::confirmed()->get();
        }

        $sentCount = 0;

        foreach ($subscribers as $subscriber) {
            // Create or find the send record
            $send = CampaignSend::firstOrCreate(
                ['campaign_id' => $campaign->id, 'subscriber_id' => $subscriber->id],
                ['token' => Str::random(48), 'created_at' => now()]
            );

            // Skip already sent
            if ($send->sent_at) {
                continue;
            }

            $body = $this->injectTracking($campaign->body, $send, $subscriber);

            try {
                Mail::to($subscriber->email, $subscriber->name)
                    ->send(new CampaignMail($campaign, $body));

                $send->update(['sent_at' => now()]);
                $sentCount++;
            } catch (\Throwable $e) {
                $send->update([
                    'failed_at' => now(),
                    'error'     => mb_substr($e->getMessage(), 0, 1000),
                ]);
            }
        }

        $campaign->update([
            'status'  => 'sent',
            'sent_at' => now(),
        ]);

        CampaignSent::dispatch($campaign, $sentCount);

        return $sentCount;
    }

    /**
     * Inject tracking pixel and replace {{unsubscribe_url}} placeholder.
     */
    public function injectTracking(string $body, CampaignSend $send, Subscriber $subscriber): string
    {
        // Replace unsubscribe placeholder
        $unsubscribeUrl = route('newsletter.unsubscribe.show', $subscriber->unsubscribe_token);
        $body = str_replace('{{unsubscribe_url}}', $unsubscribeUrl, $body);

        // Append 1x1 tracking pixel
        $pixelUrl   = route('newsletter.track.open', $send->token);
        $pixelHtml  = '<img src="' . $pixelUrl . '" width="1" height="1" alt="" style="display:block;border:0;">';

        // Insert before </body> if present, otherwise append
        if (stripos($body, '</body>') !== false) {
            $body = str_ireplace('</body>', $pixelHtml . '</body>', $body);
        } else {
            $body .= $pixelHtml;
        }

        return $body;
    }
}
