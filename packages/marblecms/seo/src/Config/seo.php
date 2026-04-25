<?php

return [
    /*
     | Blueprints excluded from the sitemap (by identifier).
     */
    'sitemap_exclude_blueprints' => [],

    /*
     | Default og:image URL used when no per-item og_image_url is set.
     */
    'og_default_image' => env('SEO_OG_DEFAULT_IMAGE', ''),

    /*
     | robots.txt content. A plain-text string or an array of lines.
     | Set to null to use the default (allow all, sitemap reference).
     */
    'robots_txt' => null,

    /*
     | Whether to output JSON-LD structured data in the <x-seo::meta> component.
     */
    'json_ld_enabled' => true,

    /*
     | Sitemap cache duration in seconds (default 6 hours).
     */
    'sitemap_cache_seconds' => 21600,
];
