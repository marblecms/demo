# Marble CMS — Demo App

<table>
<tr>
  <td width="33%"><img src="Screenshot1.png" alt="Dashboard"></td>
  <td width="33%"><img src="Screenshot2.png" alt="Item edit"></td>
  <td width="33%"><img src="Screenshot3.png" alt="Blueprint editor"></td>
</tr>
<tr>
  <td width="33%"><img src="Screenshot4.png" alt="Media library"></td>
  <td width="33%"><img src="Screenshot5.png" alt="Tree navigation"></td>
  <td width="33%"><img src="Screenshot6.png" alt="Field types"></td>
</tr>
<tr>
  <td width="33%"><img src="Screenshot7.png" alt="Site settings"></td>
  <td></td>
  <td></td>
</tr>
</table>

This repository is a Laravel 13 application pre-configured to run the [Marble CMS](https://github.com/marblecms/admin) package. It serves as the reference implementation and development sandbox.

## Requirements

- Docker & Docker Compose

That's it. Everything else runs inside the container.

## Quick Start

```bash
git clone --recurse-submodules https://github.com/marblecms/demo
cd demo
cp .env.example .env
docker compose up -d
docker compose exec app php artisan migrate:fresh --seed
docker compose exec app php artisan vendor:publish --tag=marble-assets
```

> **Note:** `--recurse-submodules` is required. The Marble CMS package lives in a [separate repo](https://github.com/marblecms/admin) and is linked here as a Git submodule under `packages/marble/admin`. Without the flag that directory will be empty and the app won't boot.
>
> Already cloned without the flag? Run:
> ```bash
> git submodule update --init
> ```

### Admin

Open [http://localhost:8080/admin](http://localhost:8080/admin) and log in with:

```
Email:    admin@admin
Password: admin
```

### Frontend

Open [http://localhost:8080](http://localhost:8080) — a full demo site is pre-seeded with navigation, blog, product catalog, contact form, and a portal-authenticated intranet section.

Portal user login (at [/portal/login](http://localhost:8080/portal/login)):

```
Email:    demo@demo.com
Password: demo
```

## Services

| Service     | URL                          |
|-------------|------------------------------|
| Admin panel | http://localhost:8080/admin  |
| Frontend    | http://localhost:8080        |
| phpMyAdmin  | http://localhost:8081        |

Database: host `db`, name `marble`, user `marble`, password `marble`.

## Seeded Demo Content

The seeder builds a full demo site across **12 blueprints** and **33 content items**:

```
Root/
├── Content/
│   └── Startpage  (home)                    ← site root, hero + feature grid
│       ├── About Us  (simple_page)
│       │   ├── Our Team  (simple_page)
│       │   │   ├── Alice Schmidt  (team_member)
│       │   │   ├── Bob Müller     (team_member)
│       │   │   └── Carol Weber    (team_member)
│       │   └── Our Story  (simple_page)
│       ├── Blog  (blog_index)
│       │   ├── Introducing Marble CMS 2.0   (blog_post, published)
│       │   ├── Building with Field Types    (blog_post, published)
│       │   ├── Multi-Site Made Easy         (blog_post, published)
│       │   ├── Content Workflows            (blog_post, draft — In Review)
│       │   └── Portal Users & Intranets     (blog_post, draft — Written)
│       ├── Products  (product_category)
│       │   ├── Software  (product_category)
│       │   │   ├── Marble CMS Pro   (product)
│       │   │   ├── Marble Analytics (product)
│       │   │   └── Marble Headless  (product)
│       │   └── Services  (product_category)
│       │       ├── Implementation   (product)
│       │       ├── Support          (product)
│       │       └── Training         (product)
│       ├── Contact  (contact_form)
│       └── Intranet  (intranet_page)        ← portal-auth gated
│           ├── Internal News  (intranet_page)
│           │   ├── Q1 2025 Results
│           │   └── New Vienna Office
│           ├── Documents      (intranet_page)
│           └── Team Directory (intranet_page)
└── Settings/
    └── Site Settings  (site_settings)
```

The two draft blog posts demonstrate the **Blog Editorial Workflow** (Written → In Review → Approved → published) with reject-with-comment support.

## Project Structure

```
app/Http/Controllers/
  FrontController.php        ← test route helper
  SearchController.php       ← GET /search full-text search
routes/web.php               ← /search route + Marble::routes() catch-all
resources/views/
  layouts/frontend.blade.php ← sticky header, 3-level CSS dropdown nav,
                               search bar, portal user indicator, dark footer
  marble-pages/
    home.blade.php            ← hero, feature grid, blog preview, product preview
    simple_page.blade.php     ← generic page with breadcrumbs + child grid
    blog_index.blade.php      ← blog post listing
    blog_post.blade.php       ← post detail with prev/next navigation
    product_category.blade.php← subcategory grid + product cards
    product.blade.php         ← product detail with sidebar pricing card
    team_member.blade.php     ← profile with bio and team sidebar
    intranet_page.blade.php   ← portal-auth gate + sidebar nav when logged in
    contact_form.blade.php    ← form + contact info sidebar
    search.blade.php          ← search results with keyword highlighting
config/marble.php             ← CMS configuration
packages/marble/admin/        ← the CMS package (git submodule)
```

## Frontend Templates

Marble resolves URL slugs to Items and renders Blade views from `resources/views/marble-pages/`. The view filename matches the blueprint identifier:

```
marble-pages/{blueprint_identifier}.blade.php
```

Each view receives an `$item` variable:

```blade
@extends('layouts.frontend')

@section('content')
    <h1>{{ $item->value('name') }}</h1>
    {!! $item->value('content') !!}

    @foreach(\Marble\Admin\Facades\Marble::children($item) as $child)
        <a href="{{ \Marble\Admin\Facades\Marble::url($child) }}">{{ $child->name() }}</a>
    @endforeach
@endsection
```

The layout reads all site-wide values (name, logo, meta tags, social links, copyright) from `Marble::settings()` automatically — no hardcoded strings.

## Key Features Demonstrated

| Feature | Where |
|---|---|
| 3-level CSS dropdown navigation | Layout header (`Marble::navigation(null, 3)`) |
| Full-text search | `GET /search?q=…` → `SearchController` |
| Portal user auth | `/portal/login` — `marble.portal.auth` middleware |
| Intranet / gated content | `intranet_page` blueprint + `Marble::isPortalAuthenticated()` |
| Editorial workflow | Blog posts: Written → In Review → Approved |
| Draft preview | Token-gated public URL for unpublished items |
| Marble Debugbar | Floating panel on frontend when admin is logged in (`MARBLE_DEBUGBAR=true`) |
| Headless REST API | `GET /api/marble/items/{blueprint}` with token auth |
| Contact forms | `contact_form` blueprint with `is_form` enabled |

## Portal Users

The Intranet section requires a portal login. Portal users are separate from CMS admin users and are managed under **System → Portal Users** in the admin.

Use the `marble.portal.auth` middleware to protect any route:

```php
Route::get('/members', MembersController::class)->middleware('marble.portal.auth');
```

Or check inline in Blade:

```blade
@if(\Marble\Admin\Facades\Marble::isPortalAuthenticated())
    Welcome, {{ Marble::portalUser()->email }}
@endif
```

## Contact Forms

The `contact_form` blueprint has **Is Form** enabled. Submissions are collected in the database and appear in the admin under the item's edit view.

```blade
<x-marble::marble-form :item="$item">
    <button type="submit">Send</button>
</x-marble::marble-form>
```

## Useful Commands

```bash
# Wipe and re-seed
docker compose exec app php artisan migrate:fresh --seed

# Health check
docker compose exec app php artisan marble:doctor

# Re-publish admin assets after package updates
docker compose exec app php artisan vendor:publish --tag=marble-assets --force

# Process scheduled publish/expire times
docker compose exec app php artisan marble:schedule-publish

# Interactive blueprint generator
docker compose exec app php artisan marble:make-blueprint

# Clear compiled views after Blade changes
docker compose exec app php artisan view:clear
```

## License

MIT
