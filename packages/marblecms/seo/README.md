# Marble SEO

SEO metadata management, XML sitemap, robots.txt, and structured data (JSON-LD) for [Marble CMS](https://github.com/marblecms/admin).

## Installation

Add to your `composer.json` repositories and require:

```bash
composer require marblecms/marble-seo
php artisan marble:seo:install
```

Optionally publish the config:

```bash
php artisan vendor:publish --tag=seo-config
```

## Configuration (`config/seo.php`)

| Key | Default | Description |
|-----|---------|-------------|
| `sitemap_exclude_blueprints` | `[]` | Blueprint identifiers to omit from the sitemap |
| `og_default_image` | `''` | Fallback og:image URL |
| `robots_txt` | `null` | Custom robots.txt content (string or array of lines); `null` = allow all + sitemap link |
| `json_ld_enabled` | `true` | Output JSON-LD structured data |
| `sitemap_cache_seconds` | `21600` | Sitemap cache TTL in seconds (6 h) |

## Usage

### Blade component

Drop the component inside your `<head>` tag:

```blade
<head>
    <x-seo::meta :item="$item" />
</head>
```

With an explicit language:

```blade
<x-seo::meta :item="$item" :language-id="$languageId" />
```

Without an item (static pages):

```blade
<x-seo::meta title="About Us" description="Learn more about us." />
```

The component outputs:

- `<title>`
- `<meta name="description">`
- `<meta property="og:title">`, `og:description`, `og:image`, `og:url`, `og:type`
- `<link rel="canonical">`
- `<meta name="robots" content="noindex, nofollow">` (when noindex is set)
- JSON-LD `WebPage` or `Article` (for `blog_post` blueprint) + `BreadcrumbList`

### Admin UI

Navigate to **Structure → SEO** in the Marble admin. Each published item shows a coverage indicator (green tick / dash) per language. Click the edit button to manage per-item, per-language SEO fields:

- SEO Title
- Meta Description (max 500 chars)
- OG Image URL
- Canonical URL override
- No-index toggle

### Sitemap

Auto-generated at `/sitemap.xml`. Includes all published items, all languages, with `xhtml:link` alternate tags. Cached for 6 hours. Cache is invalidated automatically when any item is published.

### robots.txt

Served at `/robots.txt`. Default content:

```
User-agent: *
Allow: /
Sitemap: https://example.com/sitemap.xml
```

Override via `config('seo.robots_txt')`.

## Events

The plugin listens to `Marble\Admin\Events\ItemPublished` and automatically invalidates the sitemap cache.

## Artisan Commands

| Command | Description |
|---------|-------------|
| `marble:seo:install` | Run SEO migrations |

## Database

**`seo_meta`** — one row per item per language.

| Column | Type | Notes |
|--------|------|-------|
| `id` | bigint | PK |
| `item_id` | bigint | FK → items (cascade delete) |
| `language_id` | bigint | FK → languages (cascade delete) |
| `title` | string | nullable |
| `description` | string(500) | nullable |
| `og_image_url` | string | nullable |
| `noindex` | boolean | default false |
| `canonical_url` | string | nullable |
| `created_at` / `updated_at` | timestamps | |

Unique constraint on `(item_id, language_id)`.
