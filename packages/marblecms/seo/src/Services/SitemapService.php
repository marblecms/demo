<?php

namespace MarbleCms\Seo\Services;

use Illuminate\Support\Facades\Cache;
use Marble\Admin\Models\Item;
use Marble\Admin\Models\Language;
use Marble\Admin\Facades\Marble;

class SitemapService
{
    protected const CACHE_KEY = 'seo_sitemap_xml';

    /**
     * Return the sitemap XML string, generating and caching it if necessary.
     */
    public function generate(): string
    {
        $ttl = config('seo.sitemap_cache_seconds', 21600);

        return Cache::remember(self::CACHE_KEY, $ttl, function () {
            return $this->build();
        });
    }

    /**
     * Invalidate the cached sitemap (call after items are published).
     */
    public function invalidate(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    protected function build(): string
    {
        $excludedBlueprints = config('seo.sitemap_exclude_blueprints', []);
        $languages          = Language::orderBy('id')->get();

        $items = Item::with(['blueprint'])
            ->where('status', 'published')
            ->whereNotNull('path')
            ->get()
            ->filter(function (Item $item) use ($excludedBlueprints) {
                $identifier = optional($item->blueprint)->identifier;
                return !in_array($identifier, $excludedBlueprints, true);
            });

        $entries = [];

        foreach ($items as $item) {
            $locales = [];
            foreach ($languages as $lang) {
                $url = Marble::url($item, $lang->id);
                if ($url) {
                    $locales[$lang->locale ?? $lang->code ?? 'x-default'] = $url;
                }
            }

            if (empty($locales)) {
                continue;
            }

            // Primary URL is the first language
            $primaryUrl = reset($locales);

            $entries[] = [
                'loc'     => $primaryUrl,
                'lastmod' => $item->updated_at?->toAtomString(),
                'locales' => $locales,
            ];
        }

        return view('seo::sitemap', compact('entries'))->render();
    }
}
