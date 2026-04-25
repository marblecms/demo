<?php

namespace MarbleCms\Newsletter\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use MarbleCms\Newsletter\Models\Subscriber;

class SubscriberConfirmed
{
    use Dispatchable, SerializesModels;

    public function __construct(public readonly Subscriber $subscriber)
    {
    }
}
