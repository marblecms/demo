<?php

namespace MarbleCms\Newsletter\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subscriber extends Model
{
    protected $table = 'newsletter_subscribers';

    protected $fillable = [
        'email',
        'name',
        'status',
        'confirmation_token',
        'unsubscribe_token',
        'confirmed_at',
    ];

    protected $casts = [
        'confirmed_at' => 'datetime',
    ];

    public function scopeConfirmed(Builder $query): Builder
    {
        return $query->where('status', 'confirmed');
    }

    public function lists(): BelongsToMany
    {
        return $this->belongsToMany(
            SubscriberList::class,
            'newsletter_subscriber_list',
            'subscriber_id',
            'list_id'
        )->withPivot('subscribed_at');
    }

    public function sends(): HasMany
    {
        return $this->hasMany(CampaignSend::class, 'subscriber_id');
    }
}
