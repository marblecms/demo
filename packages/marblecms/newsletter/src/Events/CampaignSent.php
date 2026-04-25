<?php

namespace MarbleCms\Newsletter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarbleCms\Newsletter\Models\Campaign;

class CampaignSent
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public readonly Campaign $campaign,
        public readonly int      $sentCount,
    ) {
    }
}
