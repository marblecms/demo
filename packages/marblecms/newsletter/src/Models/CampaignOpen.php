<?php

namespace MarbleCms\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignOpen extends Model
{
    protected $table = 'newsletter_opens';

    public $timestamps = false;

    protected $fillable = [
        'send_id',
        'opened_at',
        'ip',
        'user_agent',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(CampaignSend::class, 'send_id');
    }
}
