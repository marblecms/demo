<?php

namespace MarbleCms\Seo\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Marble\Admin\Models\Item;
use Marble\Admin\Models\Language;

class SeoMeta extends Model
{
    protected $table = 'seo_meta';

    protected $fillable = [
        'item_id',
        'language_id',
        'title',
        'description',
        'og_image_url',
        'noindex',
        'canonical_url',
    ];

    protected $casts = [
        'noindex' => 'boolean',
    ];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function language(): BelongsTo
    {
        return $this->belongsTo(Language::class);
    }
}
