<?php

namespace MarbleCms\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $table = 'newsletter_campaigns';

    protected $fillable = [
        'name',
        'subject',
        'reply_to',
        'body',
        'list_id',
        'status',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function list(): BelongsTo
    {
        return $this->belongsTo(SubscriberList::class, 'list_id');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(CampaignSend::class, 'campaign_id');
    }

    public function sentCount(): int
    {
        return $this->sends()->whereNotNull('sent_at')->count();
    }

    public function openCount(): int
    {
        return CampaignOpen::whereHas('send', fn($q) => $q->where('campaign_id', $this->id))->count();
    }

    public function clickCount(): int
    {
        return CampaignClick::whereHas('send', fn($q) => $q->where('campaign_id', $this->id))->count();
    }
}
