<?php

namespace MarbleCms\Newsletter\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubscriberList extends Model
{
    protected $table = 'newsletter_lists';

    protected $fillable = [
        'name',
        'description',
    ];

    public function subscribers(): BelongsToMany
    {
        return $this->belongsToMany(
            Subscriber::class,
            'newsletter_subscriber_list',
            'list_id',
            'subscriber_id'
        )->withPivot('subscribed_at');
    }

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'list_id');
    }
}
