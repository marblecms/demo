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
docker compose exec app php artisan marble:install
```

> **Note:** `--recurse-submodules` is required. The Marble CMS package lives in a [separate repo](https://github.com/marblecms/admin) and is linked here as a Git submodule under `packages/marble/admin`. Without the flag that directory will be empty and the app won't boot.
>
> Already cloned without the flag? Run:
> ```bash
> git submodule update --init
> ```

Open [http://localhost:8080/admin](http://localhost:8080/admin) and log in with:

```
Email:    admin@admin
Password: admin
```

## Services

| Service     | URL                          |
|-------------|------------------------------|
| Admin panel | http://localhost:8080/admin  |
| Frontend    | http://localhost:8080        |
| phpMyAdmin  | http://localhost:8081        |

Database: host `db`, name `marble`, user `marble`, password `marble`.

## Seeded Demo Content

After `marble:install` the tree looks like this:

```
Root/
├── Content/
│   └── Startpage  (slug: "")          ← site root, homepage
│       ├── About Us  (slug: about-us)
│       ├── Our Customers  (slug: our-customers)
│       │   ├── Acme Corp
│       │   ├── Globex Industries
│       │   └── Initech Solutions
│       └── Contact  (blueprint: contact_form)
└── Settings/
    └── Site Settings  (blueprint: site_settings)
```

The **Site Settings** item holds branding, SEO, contact, social, and footer fields. Access them anywhere via:

```php
Marble::settings()->value('site_name')
Marble::settings()->value('meta_title_template')
Marble::settings()->value('email')
```

The default site is pre-wired to both the **Startpage** (root item for URL resolution) and the **Site Settings** item.

## Project Structure

```
app/Http/Controllers/FrontController.php   ← frontend controller
routes/web.php                             ← Marble::routes() catch-all
resources/views/
  layouts/frontend.blade.php              ← main layout (uses Marble::settings())
  marble-pages/
    simple_page.blade.php                 ← rendered for simple_page blueprint
    contact_form.blade.php                ← rendered for contact_form blueprint
    default.blade.php                     ← fallback for unmapped blueprints
config/marble.php                         ← CMS configuration
packages/marble/admin/                    ← the CMS package (git submodule)
```

## Frontend Templates

Marble resolves URL slugs to Items and renders Blade views from `resources/views/marble-pages/`. The view is selected by blueprint identifier:

```
resources/views/marble-pages/simple_page.blade.php
resources/views/marble-pages/contact_form.blade.php
resources/views/marble-pages/default.blade.php   ← fallback
```

Each view receives an `$item` variable:

```blade
@extends('layouts.frontend')

@section('content')
    <h1>{{ $item->value('name') }}</h1>
    {!! $item->value('content') !!}

    {{-- List child pages --}}
    @foreach(\Marble\Admin\Facades\Marble::children($item) as $child)
        <a href="{{ \Marble\Admin\Facades\Marble::url($child) }}">{{ $child->name() }}</a>
    @endforeach
@endsection
```

The `layouts/frontend.blade.php` layout in this demo reads all site-wide values (name, logo, meta tags, social links, copyright) from `Marble::settings()` automatically — no hardcoded strings.

## Contact Forms

The `contact_form` blueprint has **Is Form** enabled. Submissions from the frontend are collected in the database and appear in the admin under the item's edit view. Configure recipients, success message, and success redirect per item directly in the admin.

The frontend form component:

```blade
<x-marble::marble-form :item="$item">
    <button type="submit">Send</button>
</x-marble::marble-form>
```

## Useful Commands

```bash
# First-time setup: migrate, seed, publish assets
docker compose exec app php artisan marble:install

# Wipe and re-seed (useful during development)
docker compose exec app php artisan migrate:fresh --seed

# Re-publish admin assets after package updates
docker compose exec app php artisan vendor:publish --tag=marble-assets --force

# Clear compiled views after Blade changes
docker compose exec app php artisan view:clear

# Process scheduled publish/expire times
docker compose exec app php artisan marble:publish-scheduled
```

## License

MIT
