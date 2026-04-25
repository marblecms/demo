<?php

namespace MarbleCms\Seo\Components;

use Illuminate\View\Component;
use Marble\Admin\Models\Item;
use Marble\Admin\Facades\Marble;
use MarbleCms\Seo\Services\SeoService;

/**
 * Blade component that outputs all SEO <head> tags for the current item.
 *
 * Usage:
 *   <x-seo::meta :item="$item" />
 *   <x-seo::meta :item="$item" :language-id="2" />
 *
 * When $item is null, a minimal fallback is rendered using $title / $description
 * attributes if provided.
 */
class SeoMeta extends Component
{
    public ?string $title;
    public ?string $description;
    public ?string $ogImage;
    public bool    $noindex;
    public ?string $canonical;
    public ?array  $jsonLd;

    public function __construct(
        public ?Item $item       = null,
        public ?int  $languageId = null,
        ?string      $title      = null,
        ?string      $description = null,
    ) {
        if ($item !== null) {
            $langId  = $languageId ?? Marble::primaryLanguageId();
            $service = app(SeoService::class);
            $meta    = $service->getMeta($item, $langId);

            $this->title       = $meta['title'];
            $this->description = $meta['description'];
            $this->ogImage     = $meta['ogImage'];
            $this->noindex     = $meta['noindex'];
            $this->canonical   = $meta['canonical'];
            $this->jsonLd      = $meta['jsonLd'];
        } else {
            $this->title       = $title;
            $this->description = $description;
            $this->ogImage     = config('seo.og_default_image') ?: null;
            $this->noindex     = false;
            $this->canonical   = request()->url();
            $this->jsonLd      = null;
        }
    }

    public function render()
    {
        return view('seo::components.meta');
    }
}
