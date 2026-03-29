<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Route Prefix
    |--------------------------------------------------------------------------
    */
    'route_prefix' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Admin Auth Guard
    |--------------------------------------------------------------------------
    */
    'guard' => 'marble',

    /*
    |--------------------------------------------------------------------------
    | Primary Locale
    |--------------------------------------------------------------------------
    | The default language used for non-translatable fields.
    */
    'primary_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Current Locale
    |--------------------------------------------------------------------------
    | The language used when no explicit language is passed.
    | Typically set dynamically per request in middleware.
    */
    'locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | URI Locale Prefix
    |--------------------------------------------------------------------------
    | If true, routes are prefixed with the language code: /en/about, /de/ueber-uns
    */
    'uri_locale_prefix' => false,

    /*
    |--------------------------------------------------------------------------
    | System Items ID
    |--------------------------------------------------------------------------
    | The item ID that holds system node references (settings, pages, menu).
    */
    'system_items_id' => null,

    /*
    |--------------------------------------------------------------------------
    | Entry Item ID
    |--------------------------------------------------------------------------
    | The root item for the tree. Used as fallback for user groups without
    | a specific entry item.
    */
    'entry_item_id' => 1,

    /*
    |--------------------------------------------------------------------------
    | Nav Root Item ID
    |--------------------------------------------------------------------------
    | The item whose direct children form the main frontend navigation.
    | Typically the "Content" folder (item 5 in a default install).
    */
    'nav_root_id' => 5,

    /*
    |--------------------------------------------------------------------------
    | Cache TTL
    |--------------------------------------------------------------------------
    | How long to cache items (in seconds). Set to 0 to disable.
    */
    'cache_ttl' => 3600,

    /*
    |--------------------------------------------------------------------------
    | Storage Disk
    |--------------------------------------------------------------------------
    | The disk used for media uploads.
    */
    'storage_disk' => 'public',
];
