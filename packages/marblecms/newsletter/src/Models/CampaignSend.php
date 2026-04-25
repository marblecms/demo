<?php

namespace MarbleCms\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CampaignSend extends Model
{
    protected $table = 'newsletter_sends';

    public $timestamps = false;

    protected $fillable = [
        'campaign_id',
        'subscriber_id',
        'token',
        'sent_at',
        'failed_at',
        'error',
        'created_at',
    ];

    protected $casts = [
        'sent_at'    => 'datetime',
        'failed_at'  => 'datetime',
        'created_at' => 'datetime',
    ];

    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class, 'subscriber_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function opens(): HasMany
    {
        return $this->hasMany(CampaignOpen::class, 'send_id');
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(CampaignClick::class, 'send_id');
    }
}
