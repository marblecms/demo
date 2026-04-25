<?php

namespace MarbleCms\Seo\Services;

use Marble\Admin\Models\Item;
use Marble\Admin\Facades\Marble;
use MarbleCms\Seo\Models\SeoMeta;

class SeoService
{
    /**
     * Build the full SEO data array for a given item and language.
     *
     * @return array{
     *   title: string|null,
     *   description: string|null,
     *   og_image: string|null,
     *   noindex: bool,
     *   canonical: string|null,
     *   json_ld: array|null,
     * }
     */
    public function getMeta(Item $item, int $languageId): array
    {
        $meta = SeoMeta::where('item_id', $item->id)
            ->where('language_id', $languageId)
            ->first();

        $title       = $meta?->title       ?? $item->name($languageId);
        $description = $meta?->description ?? null;
        $ogImage     = $meta?->og_image_url ?? config('seo.og_default_image') ?: null;
        $noindex     = $meta?->noindex      ?? false;
        $canonical   = $meta?->canonical_url ?? Marble::url($item, $languageId);

        $jsonLd = config('seo.json_ld_enabled', true)
            ? $this->buildJsonLd($item, $languageId, $title, $description, $ogImage, $canonical)
            : null;

        return compact('title', 'description', 'ogImage', 'noindex', 'canonical', 'jsonLd');
    }

    protected function buildJsonLd(
        Item $item,
        int $languageId,
        ?string $title,
        ?string $description,
        ?string $ogImage,
        ?string $canonical,
    ): array {
        $breadcrumb = $this->buildBreadcrumbJsonLd($item, $languageId);

        $blueprintId = optional($item->blueprint)->identifier;

        if ($blueprintId === 'blog_post') {
            $page = [
                '@context' => 'https://schema.org',
                '@type'    => 'Article',
                'headline' => $title,
                'url'      => $canonical,
            ];
        } else {
            $page = [
                '@context' => 'https://schema.org',
                '@type'    => 'WebPage',
                'name'     => $title,
                'url'      => $canonical,
            ];
        }

        if ($description) {
            $page['description'] = $description;
        }

        if ($ogImage) {
            $page['image'] = $ogImage;
        }

        return array_filter([$page, $breadcrumb]);
    }

    protected function buildBreadcrumbJsonLd(Item $item, int $languageId): ?array
    {
        try {
            $breadcrumbs = Marble::breadcrumb($item);
        } catch (\Throwable) {
            return null;
        }

        if (empty($breadcrumbs)) {
            return null;
        }

        $listItems = [];
        foreach ($breadcrumbs as $position => $crumb) {
            $listItems[] = [
                '@type'    => 'ListItem',
                'position' => $position + 1,
                'name'     => $crumb['name'] ?? '',
                'item'     => $crumb['url']  ?? '',
            ];
        }

        return [
            '@context'        => 'https://schema.org',
            '@type'           => 'BreadcrumbList',
            'itemListElement' => $listItems,
        ];
    }
}
