# Marble CMS - Project Context Dump (v2 — 2026-03-28)

## What is this?
We're building **Marble**, a Laravel CMS package (`marble/admin`). It's a rewrite of an old CodeIgniter/Laravel 5.3 CMS. The core concept: all content objects are **Items** (instances of **Blueprints**). Blueprints are admin-defined classes with configurable **Fields** (each field has a **FieldType** like textfield, image, selectbox, etc.). Items form a tree structure with parent/child relationships.

## Project Location
```
~/web/marble/                          # Laravel app root
~/web/marble/packages/marble/admin/    # The CMS package (this is the main codebase)
~/web/marble/app/Http/Controllers/FrontController.php  # Frontend controller (in the Laravel app, not the package)
~/web/marble/resources/views/front/page.blade.php      # Frontend view
```

## Naming Convention (Old → New)
| Old | New Table | New Model |
|---|---|---|
| `attribute` | `field_types` | `FieldType` |
| `node_class` | `blueprints` | `Blueprint` |
| `node_class_group` | `blueprint_groups` | `BlueprintGroup` |
| `class_attribute` | `blueprint_fields` | `BlueprintField` |
| `class_attribute_group` | `blueprint_field_groups` | `BlueprintFieldGroup` |
| `node` | `items` | `Item` |
| `node_class_attribute` | **REMOVED** (was unnecessary pivot) | — |
| `node_translation` | `item_values` | `ItemValue` |
| `language` | `languages` | `Language` |
| `user` | `marble_users` | `User` |
| `user_group` | `marble_user_groups` | `UserGroup` |
| NEW | `media` | `Media` |
| NEW | `blueprint_allowed_children` | (pivot table) |
| NEW | `user_group_allowed_blueprints` | (pivot table) |

## Key Architecture Decisions Made
1. **No more unnecessary pivot**: Old `node_class_attribute` table removed. `item_values` directly references `blueprint_field_id` + `item_id` + `language_id`
2. **JSON instead of PHP serialize**: All structured data uses JSON (`$casts = ['configuration' => 'array']`)
3. **Materialized Path** for tree: Items have a `path` column (e.g. `/1/20/22`) for efficient ancestor/descendant queries. `HasPath` trait auto-syncs it.
4. **FieldType Interface**: Each field type (textfield, image, selectbox...) implements `FieldTypeInterface` with `process()`, `serialize()`, `processInput()`, `adminComponent()`, `registerRoutes()`, etc. Designed to be packageable — third parties can create custom FieldType packages.
5. **Clean Item API**:
   ```php
   $item->value('content')         // processed, current locale
   $item->value('content', 'de')   // processed, specific language
   $item->rawValue('content')      // deserialized but not processed
   $item->values()                 // all fields as array
   $item->name()                   // shortcut
   $item->slug('de')               // full slug with parent slugs
   ```
6. **Marble Facade**: `Marble::item(42)`, `Marble::fieldType('image')`, `Marble::systemItem('settings')`, `Marble::currentLanguageId()`, `Marble::setLocale('de')`
7. **Soft Deletes** on Items
8. **Status field** on Items (`draft`, `published`, `archived`) — versioning deferred for later
9. **`allowed_child_classes`** normalized to pivot table instead of serialized array
10. **Admin UI**: Same look as old version (Bootstrap 3, jQuery, FontAwesome, same CSS classes/HTML structure), but rewritten with clean Blade, proper controllers, Eloquent relationships
11. **Caching**: Currently disabled for Item retrieval (was causing serialization issues with Eloquent models in cache). Language IDs are cached using `Cache::rememberForever` but only storing scalar values, not models.

## Tech Stack
- **Laravel 13** (latest at time of setup, composer.json allows `^11.0|^12.0|^13.0`)
- **PHP 8.4** (running in Docker because host machine has PHP 7.4)
- **SQLite** (Laravel 13 default — note: NOT MySQL despite docker-compose having MySQL)
- **Docker setup**: `docker-compose.yml` with app (PHP 8.4 + Apache), db (MySQL 8.4), phpmyadmin
- Access: `http://localhost:8080` (app), `http://localhost:8081` (phpMyAdmin)

## Bugs Fixed During Development

### 1. TreeHelper didn't show root item
**Fix**: In `TreeHelper::generate()`, load the entry item itself and set it as the tree root.

### 2. MarbleManager cache serialization crash
**Fix**: Cache only scalar `id` values, removed caching from `item()` method entirely.

### 3. Blueprint save — icon NOT NULL constraint
**Fix**: Explicit assignment with fallback: `$blueprint->icon = $request->input('icon', '') ?: '';`

### 4. Blueprint allowsAllChildren() returned false
**Fix**: Direct DB query instead of BelongsToMany (NULL pivot column issue).

### 5. Image uploads — file saved but not served
**Problem**: Laravel 13 defaults to `storage/app/private/` for `Storage::put()`. No routes existed to serve images.
**Fix**: Created `ImageController` with `show()` and `showResized()` methods. GD-based resizing with caching in `storage/app/cache/{WxH}/`. Routes registered in ServiceProvider outside admin prefix, no auth required.

### 6. FieldType JS not loading (image-edit.js etc.)
**Problem**: `image-edit.js` (defining `Attributes.Image`) was never loaded dynamically.
**Fix**: Added `getJavascripts()` method to `BaseFieldType` that converts `scripts()` array to full asset URLs. `edit_field.blade.php` now calls `Attributes.addFile()` for each script before the field template renders. Execution order: `attributes.js` (in `@yield('javascript-head')`) → field templates with `addFile()` calls → `attributes-init.js` (in `@yield('javascript')`) which loads the files and calls `Attributes.ready()`.

### 7. Image resize route 404
**Problem**: `/image/{filename}` with `where('filename', '.*')` greedily matched `/image/200/150/file.png`.
**Fix**: Register resized route FIRST with explicit constraints `where(['width' => '[0-9]+', 'height' => '[0-9]+'])`.

### 8. CKEditor missing
**Problem**: CKEditor wasn't included in the package assets.
**Fix**: User copied CKEditor to `src/Resources/assets/assets/ckeditor/` and re-published assets.

## Current File Structure
```
packages/marble/admin/
├── composer.json
└── src/
    ├── MarbleServiceProvider.php
    ├── MarbleManager.php          # Facade backend — item(), findItem(), setLocale(), fieldType()
    ├── FieldTypeRegistry.php
    ├── Config/
    │   └── marble.php
    ├── Contracts/
    │   └── FieldTypeInterface.php
    ├── Database/
    │   ├── Migrations/ (13 migration files including media table)
    │   └── Seeders/
    │       ├── FieldTypeSeeder.php
    │       └── MarbleSeeder.php
    ├── Facades/
    │   └── Marble.php
    ├── FieldTypes/
    │   ├── BaseFieldType.php      # Has getJavascripts() method
    │   ├── Textfield.php, Textblock.php, Htmlblock.php
    │   ├── Selectbox.php, Checkbox.php
    │   ├── Date.php, Datetime.php, Time.php
    │   ├── Image.php              # process() returns URL, processInput() handles upload
    │   ├── Images.php
    │   ├── ObjectRelation.php, ObjectRelationList.php
    │   └── KeyValueStore.php
    ├── Helpers/
    │   └── TreeHelper.php
    ├── Http/
    │   ├── routes.php
    │   ├── auth_routes.php
    │   └── Controllers/
    │       ├── Auth/LoginController.php
    │       ├── DashboardController.php
    │       ├── ItemController.php
    │       ├── BlueprintController.php
    │       ├── BlueprintFieldController.php
    │       ├── BlueprintFieldGroupController.php
    │       ├── BlueprintGroupController.php
    │       ├── ImageController.php    # NEW — serves images with GD resizing + cache
    │       ├── UserController.php
    │       └── UserGroupController.php
    ├── Models/
    │   ├── Blueprint.php, BlueprintField.php, BlueprintFieldGroup.php
    │   ├── BlueprintGroup.php, FieldType.php
    │   ├── Item.php, ItemValue.php
    │   ├── Language.php, Media.php    # Media model exists but unused
    │   ├── User.php, UserGroup.php
    ├── Resources/
    │   ├── assets/ (CSS, JS, fonts, images, CKEditor from old project)
    │   ├── lang/de/admin.php, lang/en/admin.php
    │   └── views/
    │       ├── layouts/ (app.blade.php, login.blade.php, tree.blade.php)
    │       ├── auth/login.blade.php
    │       ├── dashboard/view.blade.php
    │       ├── item/ (edit.blade.php, edit_field.blade.php, children.blade.php, add.blade.php)
    │       ├── blueprint/ (index.blade.php, edit.blade.php, fields.blade.php, editgroup.blade.php)
    │       ├── user/ (index.blade.php, edit.blade.php)
    │       ├── usergroup/ (index.blade.php, edit.blade.php)
    │       └── field-types/ (textfield, textblock, htmlblock, selectbox, checkbox, date, datetime, time, image, images, object_relation, object_relation_list, keyvalue_store + config views)
    └── Traits/
        └── HasPath.php
```

## Laravel App Files (outside the package)
```
app/Http/Controllers/FrontController.php   # Locale-aware, resolves by /{locale}/{slug}
resources/views/front/page.blade.php       # Simple test template showing item values
routes/web.php                             # Has /marble-test/{id} and /{locale}/{slug?} routes
```

## Routes (current)
### Frontend (in routes/web.php)
- `GET /marble-test/{id}` → `FrontController@test` (debug: load item by ID)
- `GET /{locale}/{slug?}` → `FrontController@show` (e.g. `/en/frontpage-en`, `/de/startseite`)

### Image serving (registered in ServiceProvider, no auth)
- `GET /image/{width}/{height}/{filename}` → `ImageController@showResized` (registered FIRST)
- `GET /image/{filename}` → `ImageController@show`

### Admin (prefix: /admin, auth: marble guard)
- Standard CRUD for items, blueprints, fields, users, user groups
- `POST /admin/item/save/{item}` — saves item field values (handles file uploads via enctype multipart)
- `GET /admin/item/search.json` — AJAX item search

## What's Working (Tested)
- ✅ Login/Logout with `admin@marble.local` / `password`
- ✅ Dashboard shows blueprints and users
- ✅ Tree sidebar shows root item and children
- ✅ Creating items under root
- ✅ Editing items (filling fields, saving)
- ✅ Creating blueprints and adding fields to them
- ✅ Blueprint field configuration (selectbox options etc.)
- ✅ Image upload, storage, serving with resize/cache
- ✅ Image thumbnail + filename display in admin (via dynamic JS loading)
- ✅ Frontend controller with locale-prefixed slug routing (`/en/slug`, `/de/slug`)
- ✅ Multilingual content (tested EN + DE)
- ✅ Item Value API: `$item->value('content')`, `$item->value('content', 'de')`, `$item->values()`
- ✅ CKEditor for htmlblock fields
- ✅ `Marble::setLocale('de')` sets language for current request

## What's Next — Priority Order
1. **Media Library (Mediathek)** ← CURRENT TASK
   - Extend `media` migration with `alt_text`, `title`, `folder` fields
   - MediaController with CRUD + AJAX upload
   - Admin view: thumbnail grid, upload area, search
   - Media Browser Modal: JS modal to pick existing media from any Image/Images field
   - Refactor Image FieldType to store `media_id` instead of raw file data
2. **Remaining FieldTypes test** — Images (multi), ObjectRelation, ObjectRelationList
3. **Item sorting** — drag & drop in admin
4. **Item delete** — verify soft delete + children cascade
5. **User/Permissions** — UserGroup restrictions testing
6. **Slug routing improvement** — search all blueprints, not just `simple_page`
7. **Smart caching** — re-enable item caching safely

## Important Docker Commands
```bash
docker compose up -d
docker compose down
docker compose exec app bash
docker compose exec app php artisan ...
docker compose exec app php artisan cache:clear
docker compose exec app php artisan vendor:publish --tag=marble-assets --force
```

## Laravel Setup Notes
- Auth guard `marble` configured in `config/auth.php` with `marble_users` provider
- Config published to `config/marble.php` with `entry_item_id => 1`
- Assets published to `public/vendor/marble/`
- DB is SQLite at `database/database.sqlite`
- Storage: files go to `storage/app/private/` (Laravel 13 default for local disk)
- Resize cache: `storage/app/private/cache/{WxH}/{filename}`

## Key Implementation Details

### Image serving flow
1. Upload: `Image::processInput()` → `Storage::put($hashname, $content)` → saves to `storage/app/private/`
2. DB: `item_values.value` = JSON `{"original_filename":"...", "filename":"HASH.png", "size":..., "mime_type":"...", "transformations":[]}`
3. `Image::process()` returns `url('/image/' . $raw['filename'])` 
4. `ImageController::show()` reads from Storage and serves with correct Content-Type
5. `ImageController::showResized()` resizes via GD, caches result, serves

### FieldType JS loading flow
1. `app.blade.php` `@yield('javascript-head')` → `item/edit.blade.php` loads `attributes.js`
2. `edit_field.blade.php` calls `$fieldType->getJavascripts()` → `Attributes.addFile()` for each
3. Individual field blade templates register callbacks via `Attributes.ready(function(){ ... })`
4. `app.blade.php` `@yield('javascript')` → `item/edit.blade.php` loads `attributes-init.js`
5. `attributes-init.js` dynamically injects `<script>` tags for all `addFile()`'d scripts, then calls `Attributes.ready()` which fires all registered callbacks

### MarbleManager::setLocale()
```php
public function setLocale(string $code): void
{
    $this->currentLanguageId = Cache::rememberForever("marble.language_id.{$code}", function () use ($code) {
        return Language::where('code', $code)->value('id') ?? 1;
    });
    Config::set('marble.locale', $code);
    Config::set('app.locale', $code);
}
```

## User Preferences
- Keep it short, no unnecessary blabber
- Step by step debugging (one step at a time, user executes, gives response, then next step)
- Don't be afraid to say when something is wrong
- Think first, then answer (avoid "wait, actually...")
- German/English mix is fine
