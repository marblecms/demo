<?php

namespace MarbleCms\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CampaignClick extends Model
{
    protected $table = 'newsletter_clicks';

    public $timestamps = false;

    protected $fillable = [
        'send_id',
        'url',
        'clicked_at',
        'ip',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

    public function send(): BelongsTo
    {
        return $this->belongsTo(CampaignSend::class, 'send_id');
    }
}
