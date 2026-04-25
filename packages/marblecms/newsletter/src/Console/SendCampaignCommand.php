<?php

namespace MarbleCms\Newsletter\Console;

use Illuminate\Console\Command;
use MarbleCms\Newsletter\Models\Campaign;
use MarbleCms\Newsletter\Services\MailingService;

class SendCampaignCommand extends Command
{
    protected $signature   = 'marble:newsletter:send {campaign : The campaign ID}';
    protected $description = 'Send a newsletter campaign from the command line.';

    public function handle(MailingService $mailer): int
    {
        $campaignId = $this->argument('campaign');
        $campaign   = Campaign::find($campaignId);

        if (!$campaign) {
            $this->error("Campaign #{$campaignId} not found.");
            return self::FAILURE;
        }

        if ($campaign->status === 'sent') {
            $this->warn("Campaign #{$campaignId} has already been sent.");
            return self::FAILURE;
        }

        $this->info("Sending campaign: {$campaign->name}…");

        $count = $mailer->send($campaign);

        $this->info("Done. Sent to {$count} subscriber(s).");

        return self::SUCCESS;
    }
}
