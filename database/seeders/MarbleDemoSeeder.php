<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Storage;
use Marble\Admin\Database\Seeders\MarbleSeeder;
use Marble\Admin\Models\Blueprint;
use Marble\Admin\Models\BlueprintGroup;
use Marble\Admin\Models\FieldType;
use Marble\Admin\Models\Item;
use Marble\Admin\Models\ItemValue;
use Marble\Admin\Models\Media;
use Marble\Admin\Models\MediaFolder;
use Marble\Admin\Models\Workflow;
use Marble\Admin\Models\WorkflowStep;

class MarbleDemoSeeder extends MarbleSeeder
{
    public function run(): void
    {
        parent::run();

        // ── Look up what the base seeder created ──────────────────────────────
        $startpage        = Item::whereHas('blueprint', fn($q) => $q->where('identifier', 'home'))->first();
        $siteSettingsItem = Item::whereHas('blueprint', fn($q) => $q->where('identifier', 'site_settings'))->first();
        $homeBp           = Blueprint::where('identifier', 'home')->first();
        $siteSettingsBp   = Blueprint::where('identifier', 'site_settings')->first();

        // ── Extra field types ─────────────────────────────────────────────────
        $dateFt   = FieldType::where('identifier', 'date')->first();
        $imagesFt = FieldType::where('identifier', 'images')->first();

        // ── Blueprint Groups ──────────────────────────────────────────────────
        $contentGroup = BlueprintGroup::firstOrCreate(['name' => 'Content']);
        $blogGroup    = BlueprintGroup::firstOrCreate(['name' => 'Blog']);
        $docsGroup    = BlueprintGroup::firstOrCreate(['name' => 'Documentation']);

        // ════════════════════════════════════════════════════════════════════════
        // DEMO BLUEPRINTS
        // ════════════════════════════════════════════════════════════════════════

        // ── Docs Section ─────────────────────────────────────────────────────
        $docsSection = Blueprint::firstOrCreate(
            ['identifier' => 'docs_section'],
            ['name' => 'Docs Section', 'icon' => 'book', 'blueprint_group_id' => $docsGroup->id,
             'allow_children' => true, 'list_children' => true, 'show_in_tree' => true, 'versionable' => false]
        );
        $this->ensureAllowAllChildren($docsSection->id);
        foreach ([
            ['identifier' => 'name', 'name' => 'Name', 'sort_order' => -1, 'translatable' => true],
            ['identifier' => 'slug', 'name' => 'Slug', 'sort_order' => 0,  'translatable' => true],
        ] as $f) {
            $this->ensureField($docsSection, $this->textfield, $f);
        }
        $this->ensureField($docsSection, $this->textblock, ['identifier' => 'description', 'name' => 'Description', 'sort_order' => 1, 'translatable' => true]);

        // ── Docs Page ─────────────────────────────────────────────────────────
        $docsPage = Blueprint::firstOrCreate(
            ['identifier' => 'docs_page'],
            ['name' => 'Docs Page', 'icon' => 'page', 'blueprint_group_id' => $docsGroup->id,
             'allow_children' => false, 'show_in_tree' => true, 'versionable' => true, 'api_public' => true]
        );
        foreach ([
            ['identifier' => 'name',    'name' => 'Name',    'sort_order' => -1, 'translatable' => true],
            ['identifier' => 'slug',    'name' => 'Slug',    'sort_order' => 0,  'translatable' => true],
            ['identifier' => 'section', 'name' => 'Section', 'sort_order' => 1,  'translatable' => false],
        ] as $f) {
            $this->ensureField($docsPage, $this->textfield, $f);
        }
        $this->ensureField($docsPage, $this->textblock, ['identifier' => 'description', 'name' => 'Description', 'sort_order' => 2, 'translatable' => true]);
        $this->ensureField($docsPage, $this->htmlblock, ['identifier' => 'content',     'name' => 'Content',     'sort_order' => 3, 'translatable' => true]);

        // ── Blog Index ────────────────────────────────────────────────────────
        $blogIndex = Blueprint::firstOrCreate(
            ['identifier' => 'blog_index'],
            ['name' => 'Blog Index', 'icon' => 'book', 'blueprint_group_id' => $blogGroup->id,
             'allow_children' => true, 'list_children' => true, 'show_in_tree' => true]
        );
        $this->ensureAllowAllChildren($blogIndex->id);
        foreach ([
            ['identifier' => 'name', 'name' => 'Name', 'sort_order' => -1, 'translatable' => true],
            ['identifier' => 'slug', 'name' => 'Slug', 'sort_order' => 0,  'translatable' => true],
        ] as $f) {
            $this->ensureField($blogIndex, $this->textfield, $f);
        }
        $this->ensureField($blogIndex, $this->textblock, ['identifier' => 'intro', 'name' => 'Intro', 'sort_order' => 1, 'translatable' => true]);

        // ── Blog Post ─────────────────────────────────────────────────────────
        $blogPost = Blueprint::firstOrCreate(
            ['identifier' => 'blog_post'],
            ['name' => 'Blog Post', 'icon' => 'page_white_text', 'blueprint_group_id' => $blogGroup->id,
             'allow_children' => false, 'show_in_tree' => true, 'versionable' => true, 'schedulable' => true, 'api_public' => true]
        );
        foreach ([
            ['identifier' => 'name',   'name' => 'Name',   'sort_order' => -1, 'translatable' => true],
            ['identifier' => 'slug',   'name' => 'Slug',   'sort_order' => 0,  'translatable' => true],
            ['identifier' => 'author', 'name' => 'Author', 'sort_order' => 3,  'translatable' => false],
        ] as $f) {
            $this->ensureField($blogPost, $this->textfield, $f);
        }
        $this->ensureField($blogPost, $this->textblock, ['identifier' => 'teaser',       'name' => 'Teaser',       'sort_order' => 1, 'translatable' => true]);
        $this->ensureField($blogPost, $this->htmlblock, ['identifier' => 'content',      'name' => 'Content',      'sort_order' => 2, 'translatable' => true]);
        $this->ensureField($blogPost, $dateFt,          ['identifier' => 'publish_date', 'name' => 'Publish Date', 'sort_order' => 4, 'translatable' => false]);

        // ── Blog Editorial Workflow ───────────────────────────────────────────
        $editorialWorkflow = Workflow::firstOrCreate(['name' => 'Blog Editorial']);
        $stepWritten  = WorkflowStep::firstOrCreate(['workflow_id' => $editorialWorkflow->id, 'name' => 'Written'],   ['sort_order' => 1, 'reject_enabled' => false]);
        $stepReview   = WorkflowStep::firstOrCreate(['workflow_id' => $editorialWorkflow->id, 'name' => 'In Review'], ['sort_order' => 2, 'reject_enabled' => true]);
        $stepApproved = WorkflowStep::firstOrCreate(['workflow_id' => $editorialWorkflow->id, 'name' => 'Approved'],  ['sort_order' => 3, 'reject_enabled' => true]);
        $stepReview->update(['reject_to_step_id' => $stepWritten->id]);
        $stepApproved->update(['reject_to_step_id' => $stepReview->id]);
        $blogPost->update(['workflow_id' => $editorialWorkflow->id]);

        // ── Changelog Entry ───────────────────────────────────────────────────
        $changelogEntry = Blueprint::firstOrCreate(
            ['identifier' => 'changelog_entry'],
            ['name' => 'Changelog Entry', 'icon' => 'tag', 'blueprint_group_id' => $docsGroup->id,
             'allow_children' => false, 'show_in_tree' => true, 'versionable' => false, 'api_public' => true]
        );
        foreach ([
            ['identifier' => 'name',    'name' => 'Name',    'sort_order' => -1, 'translatable' => true],
            ['identifier' => 'slug',    'name' => 'Slug',    'sort_order' => 0,  'translatable' => true],
            ['identifier' => 'version', 'name' => 'Version', 'sort_order' => 1,  'translatable' => false],
        ] as $f) {
            $this->ensureField($changelogEntry, $this->textfield, $f);
        }
        $this->ensureField($changelogEntry, $dateFt,          ['identifier' => 'release_date', 'name' => 'Release Date', 'sort_order' => 2, 'translatable' => false]);
        $this->ensureField($changelogEntry, $this->htmlblock, ['identifier' => 'content',      'name' => 'Content',      'sort_order' => 3, 'translatable' => true]);

        // ── Screenshots Page ──────────────────────────────────────────────────
        $screenshotsPage = Blueprint::firstOrCreate(
            ['identifier' => 'screenshots_page'],
            ['name' => 'Screenshots Page', 'icon' => 'photos', 'blueprint_group_id' => $contentGroup->id,
             'allow_children' => false, 'show_in_tree' => true, 'versionable' => false]
        );
        foreach ([
            ['identifier' => 'name',  'name' => 'Name',  'sort_order' => -1, 'translatable' => true],
            ['identifier' => 'slug',  'name' => 'Slug',  'sort_order' => 0,  'translatable' => true],
            ['identifier' => 'intro', 'name' => 'Intro', 'sort_order' => 1,  'translatable' => true],
        ] as $f) {
            $this->ensureField($screenshotsPage, $this->textblock, $f);
        }
        $this->ensureField($screenshotsPage, $imagesFt, ['identifier' => 'screenshots', 'name' => 'Screenshots', 'sort_order' => 2, 'translatable' => false]);

        // ════════════════════════════════════════════════════════════════════════
        // ENRICH BASE ITEMS WITH DEMO CONTENT
        // ════════════════════════════════════════════════════════════════════════

        // ── Site Settings ─────────────────────────────────────────────────────
        $this->vals($siteSettingsItem, [
            'site_name'           => 'Marble CMS',
            'tagline'             => 'A tree-based CMS for Laravel developers',
            'meta_title_template' => '%title% | Marble CMS',
            'robots'              => 'index, follow',
            'email'               => 'hello@marblecms.io',
            'copyright'           => '© 2026 Marble CMS. Open source under MIT license.',
        ]);
        $this->html($siteSettingsItem, $siteSettingsBp, 'meta_description',
            'Marble CMS is a flexible, tree-based content management system for Laravel. Multi-language, multi-site, workflows, headless API, and more.'
        );

        // ── Home ──────────────────────────────────────────────────────────────
        $this->vals($startpage, [
            'hero_title'    => "Content management.\nDone right.",
            'hero_subtitle' => 'Marble is a tree-based, multi-language CMS for Laravel. Define your content as Blueprints, manage it in a powerful admin UI, and deliver it any way you want.',
            'intro_title'   => 'Everything you need. Nothing you don\'t.',
        ]);
        $this->html($startpage, $homeBp, 'intro_text',
            '<p>Marble gives developers a <strong>flexible, structured content tree</strong> and a powerful admin UI — without the bloat. '
            . 'Define your content types as Blueprints, build your frontend your way.</p>'
            . '<ul>'
            . '<li>Multi-language &amp; multi-site out of the box</li>'
            . '<li>Headless REST API with token auth</li>'
            . '<li>Content workflows with notifications</li>'
            . '<li>Traffic analytics with D3 charts</li>'
            . '<li>A/B testing for content variants</li>'
            . '<li>Portal users for frontend authentication</li>'
            . '<li>Collaboration: comments &amp; tasks for editorial teams</li>'
            . '</ul>'
        );

        // ════════════════════════════════════════════════════════════════════════
        // DEMO CONTENT TREE
        // ════════════════════════════════════════════════════════════════════════

        // ── Documentation ─────────────────────────────────────────────────────
        $docs = $this->item($docsSection, $startpage->id, 1, null, true);
        $this->vals($docs, [
            'name'        => 'Documentation',
            'slug'        => 'docs',
            'description' => 'Everything you need to build with Marble CMS — from installation to advanced multi-site setups.',
        ]);

        $sortOrder = 0;

        $docIntro = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docIntro, ['name' => 'Introduction', 'slug' => 'introduction', 'section' => 'Getting Started',
            'description' => 'Learn what Marble CMS is, how the tree model works, and what makes it different from traditional CMSes.']);
        $this->html($docIntro, $docsPage, 'content',
            '<h2>What is Marble CMS?</h2>'
            . '<p>Marble CMS is a tree-based content management system built as a Laravel package. It ships as <code>marble/admin</code> and plugs into any Laravel 10+ application in minutes.</p>'
            . '<p>Unlike flat-page CMSes, Marble organises all content in a <strong>hierarchical tree</strong>. Every piece of content — pages, blog posts, product listings, team members, settings — is an <em>Item</em> living at a specific position in the tree.</p>'
            . '<h2>The tree model</h2>'
            . '<p>Every Item has a parent (except the Root), a sort order among its siblings, and a materialised path (e.g. <code>/1/4/12/</code>) that makes ancestor/descendant queries fast and cheap.</p>'
            . '<p>The tree is your entire site structure. You can nest items as deeply as you like, move them around, and the URL system automatically reflects the hierarchy.</p>'
            . '<h2>Blueprints define structure</h2>'
            . '<p>A <strong>Blueprint</strong> is Marble\'s equivalent of a content type. Each Blueprint defines a set of typed fields — text, rich text, images, dates, relations, and more. When you create an Item, you choose a Blueprint and fill in its fields.</p>'
            . '<p>Blueprints are created and managed in the admin UI without writing any code. You can also mark a Blueprint as <code>api_public</code> to expose its items via the headless JSON API.</p>'
            . '<h2>Key features</h2>'
            . '<ul>'
            . '<li><strong>Multi-language</strong> — Every text field can be translated per language. Items store one value per field per language.</li>'
            . '<li><strong>Multi-site</strong> — Run multiple websites from one installation, each with its own root item, domain, and settings.</li>'
            . '<li><strong>Workflow engine</strong> — Define multi-step approval workflows and assign them to Blueprints. Items move through steps with notifications and reject-with-comment support.</li>'
            . '<li><strong>Headless REST API</strong> — Expose any Blueprint as JSON. Useful for Next.js, mobile apps, or any external consumer.</li>'
            . '<li><strong>Traffic analytics</strong> — Built-in first-party page view tracking with D3 charts, referrer flow graphs, and per-item stats.</li>'
            . '<li><strong>A/B testing</strong> — Create content variants with configurable traffic splits and track impressions and conversions.</li>'
            . '<li><strong>Portal users</strong> — Separate frontend authentication for members, customers, or intranet users.</li>'
            . '<li><strong>Collaboration</strong> — Comments and tasks on every item, ideal for editorial teams.</li>'
            . '</ul>'
        );

        $docInstall = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docInstall, ['name' => 'Installation', 'slug' => 'installation', 'section' => 'Getting Started',
            'description' => 'Install Marble CMS into a new or existing Laravel application in under five minutes.']);
        $this->html($docInstall, $docsPage, 'content',
            '<h2>Requirements</h2>'
            . '<ul>'
            . '<li>PHP 8.1 or higher</li>'
            . '<li>Laravel 10 or higher</li>'
            . '<li>MySQL 8+ or PostgreSQL</li>'
            . '</ul>'
            . '<h2>Install via Composer</h2>'
            . '<pre><code>composer require marble/admin</code></pre>'
            . '<h2>Run the installer</h2>'
            . '<p>The install command publishes assets, creates the config file, and scaffolds the admin user seeder:</p>'
            . '<pre><code>php artisan marble:install</code></pre>'
            . '<h2>Run migrations</h2>'
            . '<pre><code>php artisan migrate</code></pre>'
            . '<h2>Seed the database</h2>'
            . '<p>The default seeder creates the full demo site including blueprints, items, a default site record, and an admin user:</p>'
            . '<pre><code>php artisan db:seed</code></pre>'
            . '<h2>Publishing the config</h2>'
            . '<p>To customise Marble\'s configuration, publish the config file:</p>'
            . '<pre><code>php artisan vendor:publish --tag=marble-config</code></pre>'
            . '<p>This creates <code>config/marble.php</code> in your application.</p>'
            . '<h2>Accessing the admin</h2>'
            . '<p>Navigate to <code>yoursite.com/admin</code> (the default route prefix is <code>admin</code>). Log in with the default credentials:</p>'
            . '<ul>'
            . '<li><strong>Email:</strong> admin@admin</li>'
            . '<li><strong>Password:</strong> admin</li>'
            . '</ul>'
            . '<p><strong>Important:</strong> Change the default password immediately after your first login.</p>'
        );

        $docConfig = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docConfig, ['name' => 'Configuration', 'slug' => 'configuration', 'section' => 'Getting Started',
            'description' => 'Learn what every option in config/marble.php does and how to tailor Marble to your project.']);
        $this->html($docConfig, $docsPage, 'content',
            '<h2>config/marble.php</h2>'
            . '<p>After running <code>php artisan vendor:publish --tag=marble-config</code>, you\'ll find <code>config/marble.php</code> in your application\'s config directory. The available options are:</p>'
            . '<h3>route_prefix</h3>'
            . '<p><strong>Default:</strong> <code>\'admin\'</code><br>'
            . 'The URL prefix for the Marble admin panel. With the default setting the admin is accessible at <code>/admin</code>. Change this if you prefer a different path (e.g. <code>\'cms\'</code> for <code>/cms</code>).</p>'
            . '<h3>auth_guard</h3>'
            . '<p><strong>Default:</strong> <code>\'web\'</code><br>'
            . 'The authentication guard used for admin users. If your application uses a custom guard, set it here.</p>'
            . '<h3>frontend_url</h3>'
            . '<p><strong>Default:</strong> <code>env(\'APP_URL\')</code><br>'
            . 'The base URL used when building absolute URLs for content items. Used by <code>Marble::url($item)</code>. Should match your public-facing domain.</p>'
            . '<h3>traffic_tracking</h3>'
            . '<p><strong>Default:</strong> <code>false</code><br>'
            . 'Enables the built-in traffic analytics system. Set to <code>true</code> or add <code>MARBLE_TRAFFIC_TRACKING=true</code> to your <code>.env</code> file. When enabled, the <code>InjectMarbleTracking</code> middleware injects a small JS beacon into every HTML response.</p>'
            . '<h3>api_token_lifetime</h3>'
            . '<p><strong>Default:</strong> <code>365</code> (days)<br>'
            . 'How many days an API token remains valid before expiring. Set to <code>0</code> for tokens that never expire.</p>'
        );

        $docBlueprints = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docBlueprints, ['name' => 'Blueprints', 'slug' => 'blueprints', 'section' => 'Core Concepts',
            'description' => 'Blueprints are Marble\'s content type definitions. Learn how to create fields, set permissions, and attach workflows.']);
        $this->html($docBlueprints, $docsPage, 'content',
            '<h2>What is a Blueprint?</h2>'
            . '<p>A Blueprint is the schema for a type of content. Think of it as a database table definition — but managed in the admin UI without writing migrations. Every Item in Marble is an instance of exactly one Blueprint.</p>'
            . '<h2>Blueprint attributes</h2>'
            . '<ul>'
            . '<li><strong>name</strong> — Human-readable label shown in the admin.</li>'
            . '<li><strong>identifier</strong> — Machine-readable slug used in code (e.g. <code>blog_post</code>).</li>'
            . '<li><strong>icon</strong> — Icon name displayed in the admin tree.</li>'
            . '<li><strong>blueprint_group</strong> — Groups related blueprints in the admin sidebar.</li>'
            . '<li><strong>allow_children</strong> — Whether items of this type can have child items.</li>'
            . '<li><strong>api_public</strong> — If true, items of this blueprint are accessible via the headless JSON API.</li>'
            . '<li><strong>versionable</strong> — If true, Marble keeps a version history for every save.</li>'
            . '<li><strong>schedulable</strong> — If true, items can have <code>published_at</code> and <code>expires_at</code> dates for scheduled publishing.</li>'
            . '<li><strong>is_form</strong> — Marks the blueprint as a form (special handling in the admin).</li>'
            . '</ul>'
            . '<h2>Field types</h2>'
            . '<p>Each Blueprint has zero or more fields. Available field types:</p>'
            . '<ul>'
            . '<li><strong>textfield</strong> — Single-line text input.</li>'
            . '<li><strong>textblock</strong> — Multi-line plain text area.</li>'
            . '<li><strong>htmlblock</strong> — Rich text editor (CKEditor 5). Stores HTML.</li>'
            . '<li><strong>file</strong> — Single file upload, linked to the Media Library.</li>'
            . '<li><strong>files</strong> — Multiple file uploads.</li>'
            . '<li><strong>image</strong> — Single image with focal point support.</li>'
            . '<li><strong>images</strong> — Multiple images.</li>'
            . '<li><strong>date</strong> — Date picker field.</li>'
            . '<li><strong>repeater</strong> — A list of sub-items, each with their own fields.</li>'
            . '<li><strong>relation</strong> — Links this item to another item in the tree.</li>'
            . '</ul>'
            . '<h2>Blueprint groups</h2>'
            . '<p>Blueprint groups are display-only categories in the admin sidebar. They help editors navigate a large set of blueprints. Group assignments have no effect on content structure.</p>'
            . '<h2>Allowing children</h2>'
            . '<p>If <code>allow_children</code> is true, you can additionally configure which blueprints are allowed as children. Either allow all children (a wildcard rule) or list specific blueprints. This controls the "Create child item" dropdown in the admin.</p>'
            . '<h2>Assigning a workflow</h2>'
            . '<p>Select a workflow in the blueprint edit form. Items of this blueprint type will then move through the workflow\'s steps instead of going directly to published status.</p>'
        );

        $docItems = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docItems, ['name' => 'Items & the Content Tree', 'slug' => 'items-and-the-content-tree', 'section' => 'Core Concepts',
            'description' => 'Items are the universal content unit in Marble. Learn the data model, status lifecycle, and how to query items.']);
        $this->html($docItems, $docsPage, 'content',
            '<h2>The Item model</h2>'
            . '<p>Every piece of content is an <code>Item</code>. The Item model has these core attributes:</p>'
            . '<ul>'
            . '<li><strong>id</strong> — Auto-incrementing primary key.</li>'
            . '<li><strong>blueprint_id</strong> — Which Blueprint this item is an instance of.</li>'
            . '<li><strong>parent_id</strong> — The parent item (null for the root).</li>'
            . '<li><strong>path</strong> — Materialised path, e.g. <code>/1/3/12/</code>. Used for fast tree queries.</li>'
            . '<li><strong>status</strong> — Either <code>published</code> or <code>draft</code>.</li>'
            . '<li><strong>sort_order</strong> — Position among siblings (ascending).</li>'
            . '<li><strong>show_in_nav</strong> — Whether this item appears in navigation queries.</li>'
            . '<li><strong>published_at</strong> — Optional scheduled publish timestamp.</li>'
            . '<li><strong>expires_at</strong> — Optional scheduled expiry timestamp.</li>'
            . '</ul>'
            . '<h2>Item values</h2>'
            . '<p>Field content is stored in the <code>ItemValue</code> model. Each record links an item, a blueprint field, and a language to a string value. This allows full per-language content without schema changes.</p>'
            . '<p>Access field values in your templates or code:</p>'
            . '<pre><code>$item->value(\'field_name\')        // read a field value (current language)'
            . "\n" . '$item->name()                      // returns the value of the \'name\' field'
            . "\n" . '$item->slug($languageId)            // returns the public URL path'
            . '</code></pre>'
            . '<h2>Status lifecycle</h2>'
            . '<p>Items start as <code>draft</code>. They become <code>published</code> when an editor publishes them (or when a workflow reaches its final step). The front-end router only shows published items.</p>'
            . '<p>If the Blueprint is schedulable, the item can have a <code>published_at</code> date and will become visible automatically when that time passes. Likewise, <code>expires_at</code> removes the item from public view without deleting it.</p>'
        );

        $docSites = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docSites, ['name' => 'Sites & Languages', 'slug' => 'sites-and-languages', 'section' => 'Core Concepts',
            'description' => 'Run multiple websites and serve multiple languages from a single Marble installation.']);
        $this->html($docSites, $docsPage, 'content',
            '<h2>The Site model</h2>'
            . '<p>A <code>Site</code> record maps a domain to a root item in the content tree. Key attributes:</p>'
            . '<ul>'
            . '<li><strong>name</strong> — Display name for the site.</li>'
            . '<li><strong>domain</strong> — Exact hostname match (e.g. <code>mysite.com</code>). Leave null for the default site.</li>'
            . '<li><strong>root_item_id</strong> — The item at the top of this site\'s public content tree. Items above the root are invisible to visitors.</li>'
            . '<li><strong>settings_item_id</strong> — A <code>site_settings</code> item providing the site\'s branding, SEO defaults, and contact info.</li>'
            . '<li><strong>active</strong> — Toggle a site on/off without deleting it.</li>'
            . '<li><strong>is_default</strong> — One site must be marked as default. It is used when no domain matches the incoming request.</li>'
            . '</ul>'
            . '<h2>Languages</h2>'
            . '<p>Languages are created in Admin → Settings → Languages. Each language has a <code>code</code> (e.g. <code>en</code>) and a <code>name</code>.</p>'
            . '<p>When an item is created, Marble stores one <code>ItemValue</code> per language for every translatable field. Non-translatable fields (like price or publication date) share a single value across all languages.</p>'
            . '<p>In your frontend code, set the active language for the current request:</p>'
            . '<pre><code>Marble::setLanguageById($languageId);</code></pre>'
            . '<p>All subsequent calls to <code>$item->value()</code>, <code>$item->name()</code>, and <code>$item->slug()</code> will return values for the active language.</p>'
        );

        $docRouting = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docRouting, ['name' => 'Routing', 'slug' => 'routing', 'section' => 'Core Concepts',
            'description' => 'Understand how Marble resolves URLs to Items — including aliases and site boundaries.']);
        $this->html($docRouting, $docsPage, 'content',
            '<h2>The MarbleRouter</h2>'
            . '<p>Marble resolves incoming requests to Items using <code>MarbleRouter::resolve($path, $languageId, $site)</code>. It returns the matching <code>Item</code> or <code>null</code> if nothing is found.</p>'
            . '<h2>Canonical slug resolution</h2>'
            . '<p>Each item\'s public URL is derived from the <code>slug</code> field of every ancestor from the site\'s root item down to the item itself. The root item\'s own slug is stripped from the URL.</p>'
            . '<p>Example: if your root item has slug <code>startpage</code> and a child has slug <code>about-us</code>, the public URL is <code>/about-us</code> (not <code>/startpage/about-us</code>).</p>'
            . '<pre><code>$item->slug($languageId)  // always returns the stripped public URL</code></pre>'
            . '<h2>URL aliases</h2>'
            . '<p>Any item can have one or more URL aliases — custom slugs that point to the item. Aliases are managed in the item\'s Aliases tab in the admin.</p>'
            . '<p>The router checks aliases if no canonical match is found. Aliases must belong to the same site\'s content tree.</p>'
            . '<h2>Site boundary</h2>'
            . '<p>Marble enforces a site boundary: only items that are descendants of the site\'s <code>root_item_id</code> are accessible via that site\'s domain. Items in another site\'s subtree cannot be reached, even with an alias.</p>'
        );

        $docMedia = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docMedia, ['name' => 'Media & Files', 'slug' => 'media-and-files', 'section' => 'Core Concepts',
            'description' => 'Upload, organise, and use images and files across your content with the Marble Media Library.']);
        $this->html($docMedia, $docsPage, 'content',
            '<h2>Field types for media</h2>'
            . '<p>Marble provides four media field types:</p>'
            . '<ul>'
            . '<li><strong>file</strong> — A single file (any type).</li>'
            . '<li><strong>files</strong> — Multiple files.</li>'
            . '<li><strong>image</strong> — A single image with optional focal point.</li>'
            . '<li><strong>images</strong> — Multiple images.</li>'
            . '</ul>'
            . '<h2>Stored value format</h2>'
            . '<p>Media field values are stored as JSON. A single file value looks like:</p>'
            . '<pre><code>{'
            . "\n  \"url\": \"/storage/media/photo.jpg\","
            . "\n  \"original_filename\": \"team-photo.jpg\","
            . "\n  \"mime_type\": \"image/jpeg\","
            . "\n  \"size\": 245760,"
            . "\n  \"width\": 1920,"
            . "\n  \"height\": 1080"
            . "\n}</code></pre>"
            . '<h2>Accessing media in Blade</h2>'
            . '<pre><code>@php $photo = $item->value(\'photo\'); @endphp'
            . "\n@if(!empty(\$photo['url']))"
            . "\n    <img src=\"{{ \$photo['url'] }}\" alt=\"{{ \$photo['original_filename'] }}\">"
            . "\n@endif</code></pre>"
            . '<h2>Media Library</h2>'
            . '<p>The Media Library (Admin → Media) lets editors upload, browse, and organise files in folders. Any uploaded file can be reused across multiple items without uploading it again.</p>'
            . '<h2>Focal point support</h2>'
            . '<p>Image fields include a focal point picker. Editors click the point of interest on the image, and the focal point coordinates are stored alongside the URL. Use these in your CSS to ensure the subject remains visible when the image is cropped.</p>'
        );

        $docDashboard = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docDashboard, ['name' => 'Dashboard', 'slug' => 'dashboard', 'section' => 'Admin Interface',
            'description' => 'An overview of the Marble admin dashboard and the widgets it provides.']);
        $this->html($docDashboard, $docsPage, 'content',
            '<h2>The Dashboard</h2>'
            . '<p>The Dashboard is the first screen you see after logging in. It provides a quick overview of your CMS activity and surfaces items that need your attention.</p>'
            . '<h2>Pending Review</h2>'
            . '<p>Items currently sitting in a workflow step that is assigned to your user group. These are waiting for your action — advance or reject them to keep content flowing.</p>'
            . '<h2>My Drafts</h2>'
            . '<p>Draft items you have recently edited, giving you quick access to work in progress.</p>'
            . '<h2>Upcoming Deadlines</h2>'
            . '<p>Items with an <code>expires_at</code> date approaching. Useful for time-sensitive campaigns or seasonal content.</p>'
            . '<h2>Quick Create</h2>'
            . '<p>The Quick Create button at the top right of the dashboard lets you create a new item of any blueprint type you have permission to create.</p>'
            . '<h2>Activity log</h2>'
            . '<p>A chronological log of recent CMS actions — who created, edited, or published what. Useful for accountability in team environments.</p>'
        );

        $docUsers = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docUsers, ['name' => 'Users & Groups', 'slug' => 'users-and-groups', 'section' => 'Admin Interface',
            'description' => 'Manage CMS users, user groups, and fine-grained blueprint permissions.']);
        $this->html($docUsers, $docsPage, 'content',
            '<h2>Users</h2>'
            . '<p>CMS users are stored in the <code>marble_users</code> table. Each user has:</p>'
            . '<ul>'
            . '<li><strong>name</strong> — Display name.</li>'
            . '<li><strong>email</strong> — Login email.</li>'
            . '<li><strong>password</strong> — Hashed password.</li>'
            . '<li><strong>user_group_id</strong> — The group this user belongs to.</li>'
            . '<li><strong>language</strong> — Preferred interface language.</li>'
            . '</ul>'
            . '<h2>User Groups</h2>'
            . '<p>Users are organised into groups. Groups control what users can see and do:</p>'
            . '<ul>'
            . '<li><strong>entry_item_id</strong> — Restricts the user to a specific subtree of the content tree. Users in this group only see items under this item.</li>'
            . '<li><strong>Blueprint permissions</strong> — Per-blueprint: <code>can_create</code>, <code>can_read</code>, <code>can_update</code>, <code>can_delete</code>. Use an allow-all rule for admin groups.</li>'
            . '<li><strong>System permissions</strong> — <code>can_create_users</code>, <code>can_edit_users</code>, <code>can_delete_users</code>, <code>can_create_blueprints</code>, <code>can_edit_blueprints</code>, <code>can_delete_blueprints</code>, and equivalents for groups.</li>'
            . '</ul>'
            . '<h2>Restricting tree access</h2>'
            . '<p>Setting <code>entry_item_id</code> on a group is the primary way to give editors access to only their own section. For example, a "Marketing Team" group might have <code>entry_item_id</code> pointing to the Marketing folder, meaning its members cannot navigate outside that branch.</p>'
        );

        $docWorkflow = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docWorkflow, ['name' => 'Workflow Engine', 'slug' => 'workflow-engine', 'section' => 'Admin Interface',
            'description' => 'Set up multi-step approval workflows for any content type, with per-step permissions, notifications, and reject actions.']);
        $this->html($docWorkflow, $docsPage, 'content',
            '<h2>What is a Workflow?</h2>'
            . '<p>A Workflow is a named sequence of steps that an item must pass through before it can be published. Common examples: "Written → In Review → Approved" or "Draft → Legal → Marketing → Live".</p>'
            . '<h2>Workflow steps</h2>'
            . '<p>Each step has a name, a sort order, and optionally:</p>'
            . '<ul>'
            . '<li><strong>reject_enabled</strong> — Whether a reviewer can send the item back to a previous step.</li>'
            . '<li><strong>reject_to_step_id</strong> — Which step to send the item back to on rejection.</li>'
            . '</ul>'
            . '<h2>Assigning workflows to blueprints</h2>'
            . '<p>In Admin → Blueprints → Edit, select a workflow from the dropdown. Items of that blueprint type will now require workflow approval before publishing.</p>'
            . '<h2>Per-step group restrictions</h2>'
            . '<p>You can restrict which user groups can advance an item from each step. This means only certain groups (e.g. "Legal", "Editorial Board") see workflow actions for specific steps.</p>'
            . '<h2>Reject with comment</h2>'
            . '<p>When a reviewer rejects an item, they write a comment explaining the required changes. The item moves back to the configured step, and the comment appears in the item\'s workflow history.</p>'
            . '<h2>Notifications</h2>'
            . '<p>Configure per-step notifications to alert users or groups when an item enters a step. Notifications can be delivered as in-app CMS bells or by email.</p>'
        );

        $docTraffic = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docTraffic, ['name' => 'Traffic & Analytics', 'slug' => 'traffic-and-analytics', 'section' => 'Admin Interface',
            'description' => 'Marble\'s built-in first-party analytics track page views, sessions, and referrer flows without third-party scripts.']);
        $this->html($docTraffic, $docsPage, 'content',
            '<h2>Enabling traffic tracking</h2>'
            . '<p>Traffic tracking is disabled by default. To enable it, add the following to your <code>.env</code> file:</p>'
            . '<pre><code>MARBLE_TRAFFIC_TRACKING=true</code></pre>'
            . '<p>Or set <code>traffic_tracking => true</code> in <code>config/marble.php</code>.</p>'
            . '<h2>How it works</h2>'
            . '<p>The <code>InjectMarbleTracking</code> middleware automatically appends a small JavaScript beacon to every HTML response. The beacon fires after the page loads and POSTs to <code>/admin-track</code> with:</p>'
            . '<ul>'
            . '<li><code>item_id</code> — The ID of the current content item.</li>'
            . '<li><code>language_id</code> — The active language.</li>'
            . '<li><code>path</code> — The current URL path.</li>'
            . '<li><code>referrer</code> — The HTTP referrer (if any).</li>'
            . '</ul>'
            . '<h2>The MarblePageview model</h2>'
            . '<p>Each tracked visit is stored as a <code>MarblePageview</code> record with: <code>item_id</code>, <code>language_id</code>, <code>site_id</code>, <code>path</code>, <code>referrer</code>, <code>session_id</code>, <code>ip</code>, and <code>created_at</code>.</p>'
            . '<h2>Per-item traffic view</h2>'
            . '<p>In the item edit view, click the Traffic tab to see a daily bar chart (rendered with D3.js), total view and session counts, and a flow graph showing incoming referrers and outgoing link clicks.</p>'
            . '<h2>Site-wide analytics</h2>'
            . '<p>Admin → Traffic shows a table of top pages ordered by view count, with sparklines for the last 30 days.</p>'
        );

        $docAB = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docAB, ['name' => 'A/B Testing', 'slug' => 'ab-testing', 'section' => 'Admin Interface',
            'description' => 'Create content variants with configurable traffic splits and track impressions and conversions.']);
        $this->html($docAB, $docsPage, 'content',
            '<h2>What is A/B Testing in Marble?</h2>'
            . '<p>Marble\'s A/B testing lets you create variants of any item — overriding specific field values for a percentage of visitors — and measure which variant performs better.</p>'
            . '<h2>ItemVariant</h2>'
            . '<p>An <code>ItemVariant</code> record is attached to an item and stores:</p>'
            . '<ul>'
            . '<li><strong>name</strong> — A label for the variant (e.g. "Blue CTA button").</li>'
            . '<li><strong>traffic_split</strong> — Percentage of visitors (0–100%) who see the variant.</li>'
            . '<li><strong>is_active</strong> — Toggle the variant on or off without deleting it.</li>'
            . '<li><strong>impressions_a / impressions_b</strong> — Counters for how many times each variant has been shown.</li>'
            . '<li><strong>conversions_a / conversions_b</strong> — Counters for how many times each variant led to a conversion event.</li>'
            . '</ul>'
            . '<h2>ItemVariantValue</h2>'
            . '<p>Each <code>ItemVariantValue</code> overrides a specific blueprint field for the variant. Only the fields you specify are different; all other fields fall back to the item\'s canonical values.</p>'
            . '<h2>Creating a variant</h2>'
            . '<p>In the item edit view, go to the Variants tab, click "Add Variant", set the traffic split, and override the fields you want to test.</p>'
            . '<h2>How it works on the frontend</h2>'
            . '<p>When <code>MarbleRouter</code> resolves an item that has active variants, it uses the visitor\'s session to deterministically assign them to variant A or B. The session assignment persists for the visitor\'s entire session, so they always see the same variant.</p>'
            . '<h2>Tracking conversions</h2>'
            . '<p>Fire a conversion event from JavaScript:</p>'
            . '<pre><code>MarbleAB.convert();</code></pre>'
            . '<p>This increments the conversion counter for whichever variant the visitor is currently seeing.</p>'
        );

        $docCollab = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docCollab, ['name' => 'Collaboration', 'slug' => 'collaboration', 'section' => 'Admin Interface',
            'description' => 'Comments and tasks on every item make it easy for editorial teams to review, annotate, and organise their work.']);
        $this->html($docCollab, $docsPage, 'content',
            '<h2>The Collaboration tab</h2>'
            . '<p>Every item in Marble has a Collaboration tab in the edit view. Here team members can leave comments and manage tasks without leaving the CMS.</p>'
            . '<h2>Item Comments</h2>'
            . '<p>Comments are stored as <code>ItemComment</code> records with <code>item_id</code>, <code>user_id</code>, <code>content</code>, and <code>created_at</code>. Any CMS user can add a comment to any item they can access.</p>'
            . '<p>Comments are ideal for review notes, questions, and feedback during the editorial workflow.</p>'
            . '<h2>Item Tasks</h2>'
            . '<p><code>ItemTask</code> records represent to-do items attached to a content item:</p>'
            . '<ul>'
            . '<li><strong>title</strong> — What needs to be done.</li>'
            . '<li><strong>assigned_to</strong> — Which user is responsible.</li>'
            . '<li><strong>due_date</strong> — Optional deadline.</li>'
            . '<li><strong>done</strong> — Mark complete when finished.</li>'
            . '</ul>'
            . '<h2>Use cases</h2>'
            . '<ul>'
            . '<li>Leave a review comment when rejecting a workflow step.</li>'
            . '<li>Assign a task to a designer to add an image before publishing.</li>'
            . '<li>Track to-dos for a content refresh campaign.</li>'
            . '</ul>'
        );

        $docBlade = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docBlade, ['name' => 'Blade Templates', 'slug' => 'blade-templates', 'section' => 'Frontend',
            'description' => 'How Marble dispatches requests to Blade views and the helper facades available in your templates.']);
        $this->html($docBlade, $docsPage, 'content',
            '<h2>Template dispatch convention</h2>'
            . '<p>Marble maps the blueprint identifier of the resolved item to a Blade view. By convention, views live at:</p>'
            . '<pre><code>resources/views/marble-pages/{blueprint_identifier}.blade.php</code></pre>'
            . '<p>For example, an item with blueprint <code>blog_post</code> will render <code>resources/views/marble-pages/blog_post.blade.php</code>. The resolved item is available as <code>$item</code>.</p>'
            . '<h2>Marble facade helpers</h2>'
            . '<ul>'
            . '<li><code>Marble::url($item)</code> — Returns the absolute URL for an item.</li>'
            . '<li><code>Marble::settings()</code> — Returns the current site\'s settings item.</li>'
            . '<li><code>Marble::navigation($parent, $depth)</code> — Returns published items with <code>show_in_nav = true</code>, optionally scoped to a parent item and limited to a depth.</li>'
            . '<li><code>Marble::children($item)</code> — Returns published direct children of the given item.</li>'
            . '<li><code>Marble::portalUser()</code> — Returns the currently logged-in portal user, or null.</li>'
            . '<li><code>Marble::isPortalAuthenticated()</code> — Returns true if a portal user is authenticated.</li>'
            . '</ul>'
            . '<h2>Breadcrumb component</h2>'
            . '<p>The <code>&lt;x-breadcrumb :item="$item" /&gt;</code> component renders a breadcrumb trail from the site root down to the current item. Override its view at <code>resources/views/components/breadcrumb.blade.php</code> if you need custom markup.</p>'
        );

        $docFrontRouting = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docFrontRouting, ['name' => 'Routing & URLs', 'slug' => 'frontend-routing', 'section' => 'Frontend',
            'description' => 'How URLs are constructed from item slugs and how URL aliases work.']);
        $this->html($docFrontRouting, $docsPage, 'content',
            '<h2>URL construction</h2>'
            . '<p>A content item\'s public URL is built by concatenating the <code>slug</code> field values of every ancestor from the site root down to the item, separated by <code>/</code>. The site root item\'s own slug is stripped.</p>'
            . '<p>Example:</p>'
            . '<pre><code>Root item:   slug = "startpage"   (stripped)'
            . "\nProducts:    slug = \"products\""
            . "\nShoes:       slug = \"shoes\""
            . "\n→ Public URL: /products/shoes"
            . '</code></pre>'
            . '<h2>item->slug()</h2>'
            . '<p><code>$item->slug($languageId)</code> always returns the stripped, public-facing URL. You never need to manually concatenate paths.</p>'
            . '<h2>URL aliases</h2>'
            . '<p>URL aliases allow custom paths that point to any item. Create them in the item\'s Aliases tab in the admin. The router checks canonical slugs first and falls back to aliases if no match is found.</p>'
            . '<h2>Site boundary enforcement</h2>'
            . '<p>Marble will not resolve aliases to items outside the current site\'s subtree. This prevents cross-site content leakage when running multiple sites from one installation.</p>'
        );

        $docHeadless = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docHeadless, ['name' => 'Headless API', 'slug' => 'headless-api', 'section' => 'Frontend',
            'description' => 'Use Marble as a headless CMS. Expose any Blueprint as JSON and query it from any frontend or mobile app.']);
        $this->html($docHeadless, $docsPage, 'content',
            '<h2>Enabling the API for a Blueprint</h2>'
            . '<p>Set <code>api_public = true</code> on any Blueprint to expose its items via the REST API. This can be toggled in Admin → Blueprints → Edit.</p>'
            . '<h2>Fetching content</h2>'
            . '<pre><code>GET /api/content/{slug}</code></pre>'
            . '<p>Returns a JSON object:</p>'
            . '<pre><code>{'
            . "\n  \"id\": 42,"
            . "\n  \"name\": \"My Page\","
            . "\n  \"slug\": \"/my-page\","
            . "\n  \"blueprint\": \"simple_page\","
            . "\n  \"values\": {"
            . "\n    \"hero_title\": \"Hello World\","
            . "\n    \"content\": \"<p>...</p>\""
            . "\n  },"
            . "\n  \"children\": []"
            . "\n}"
            . '</code></pre>'
            . '<h2>Authentication</h2>'
            . '<p>API requests must include a bearer token:</p>'
            . '<pre><code>Authorization: Bearer your-api-token</code></pre>'
            . '<p>Tokens are managed in the <code>marble_api_tokens</code> table.</p>'
            . '<h2>Query parameters</h2>'
            . '<ul>'
            . '<li><code>?blueprint=blog_post</code> — Filter results to a specific blueprint.</li>'
            . '<li><code>?with_children=1</code> — Include child items in the response.</li>'
            . '</ul>'
        );

        $docPortal = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docPortal, ['name' => 'Portal Users', 'slug' => 'portal-users', 'section' => 'Frontend',
            'description' => 'Add frontend authentication to your Marble site for member portals, intranets, or customer areas.']);
        $this->html($docPortal, $docsPage, 'content',
            '<h2>What are Portal Users?</h2>'
            . '<p>Portal Users are separate from CMS admin users. They log in on the public frontend and can access gated content. This is Marble\'s built-in solution for member portals, intranets, and customer-only areas.</p>'
            . '<h2>The PortalUser model</h2>'
            . '<ul>'
            . '<li><strong>email</strong> — Login email.</li>'
            . '<li><strong>password</strong> — Hashed password.</li>'
            . '<li><strong>enabled</strong> — Toggle access without deleting the record.</li>'
            . '<li><strong>item_id</strong> — Optional link to a content item (e.g. a "Member" blueprint) for profile data.</li>'
            . '</ul>'
            . '<h2>Authentication endpoints</h2>'
            . '<ul>'
            . '<li><code>POST /portal/login</code> — Accepts <code>email</code> and <code>password</code>. Logs in the portal user.</li>'
            . '<li><code>POST /portal/logout</code> — Logs out the portal user.</li>'
            . '</ul>'
            . '<h2>Protecting routes</h2>'
            . '<p>Apply the <code>marble.portal.auth</code> middleware to any route or route group:</p>'
            . '<pre><code>Route::middleware(\'marble.portal.auth\')->group(function () {'
            . "\n    Route::get('/members', ...);"
            . "\n});"
            . '</code></pre>'
            . '<h2>Checking authentication in Blade</h2>'
            . '<pre><code>@if(Marble::isPortalAuthenticated())'
            . "\n    Welcome, {{ Marble::portalUser()->email }}"
            . "\n@endif"
            . '</code></pre>'
            . '<h2>Linking to content</h2>'
            . '<p>If <code>item_id</code> is set on the portal user, <code>Marble::portalUser()->item</code> gives you the full linked item with all its field values — useful for showing profile pages.</p>'
        );

        $docMultisite = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docMultisite, ['name' => 'Multi-site Setup', 'slug' => 'multi-site-setup', 'section' => 'Advanced',
            'description' => 'Run multiple websites from a single Marble installation, each with its own domain, content tree, and settings.']);
        $this->html($docMultisite, $docsPage, 'content',
            '<h2>Overview</h2>'
            . '<p>One Marble installation can serve multiple distinct websites. Each website is a <code>Site</code> record that maps a domain name to a root item in the shared content tree.</p>'
            . '<h2>Creating an additional site</h2>'
            . '<ol>'
            . '<li>In Admin → Sites, click "Add Site".</li>'
            . '<li>Enter the <strong>domain</strong> (exact hostname, e.g. <code>shop.mycompany.com</code>).</li>'
            . '<li>Choose a <strong>root item</strong> — the top of this site\'s public content tree. Items above this are invisible to visitors on this domain.</li>'
            . '<li>Optionally create a <code>site_settings</code> item for site-specific branding and link it as the <strong>settings item</strong>.</li>'
            . '</ol>'
            . '<h2>Domain resolution</h2>'
            . '<p>When a request arrives, Marble matches <code>request()->getHost()</code> against registered site domains. If no domain matches, the site with <code>is_default = true</code> is used.</p>'
            . '<h2>Per-site content</h2>'
            . '<p>Content is physically stored in the same tree. The site boundary is enforced at routing time — only items that are descendants of the site\'s <code>root_item_id</code> are visible on that domain.</p>'
            . '<h2>Per-site settings</h2>'
            . '<p>Give each site its own <code>site_settings</code> item to provide separate logos, taglines, contact details, and meta defaults. Access via <code>Marble::settings()</code> — Marble automatically returns the correct settings for the current site.</p>'
        );

        $docConfigRef = $this->item($docsPage, $docs->id, $sortOrder++, null, false);
        $this->vals($docConfigRef, ['name' => 'Configuration Reference', 'slug' => 'configuration-reference', 'section' => 'Advanced',
            'description' => 'Complete reference for all options available in config/marble.php.']);
        $this->html($docConfigRef, $docsPage, 'content',
            '<h2>config/marble.php — Complete Reference</h2>'
            . '<table>'
            . '<thead><tr><th>Key</th><th>Type</th><th>Default</th><th>Description</th></tr></thead>'
            . '<tbody>'
            . '<tr><td><code>route_prefix</code></td><td>string</td><td><code>\'admin\'</code></td><td>URL prefix for the admin panel.</td></tr>'
            . '<tr><td><code>auth_guard</code></td><td>string</td><td><code>\'web\'</code></td><td>Laravel auth guard used for admin authentication.</td></tr>'
            . '<tr><td><code>frontend_url</code></td><td>string</td><td><code>env(\'APP_URL\')</code></td><td>Base URL for building absolute content URLs.</td></tr>'
            . '<tr><td><code>traffic_tracking</code></td><td>bool</td><td><code>false</code></td><td>Enable built-in traffic analytics. Set <code>MARBLE_TRAFFIC_TRACKING=true</code> in .env.</td></tr>'
            . '<tr><td><code>api_token_lifetime</code></td><td>int</td><td><code>365</code></td><td>API token lifetime in days. Set to 0 for non-expiring tokens.</td></tr>'
            . '</tbody>'
            . '</table>'
            . '<h2>Environment variables</h2>'
            . '<ul>'
            . '<li><code>MARBLE_TRAFFIC_TRACKING</code> — Set to <code>true</code> to enable traffic analytics.</li>'
            . '<li><code>APP_URL</code> — Used as the default value for <code>frontend_url</code>.</li>'
            . '</ul>'
            . '<h2>Example config/marble.php</h2>'
            . '<pre><code>return ['
            . "\n    'route_prefix'       => env('MARBLE_ADMIN_PREFIX', 'admin'),"
            . "\n    'auth_guard'         => 'web',"
            . "\n    'frontend_url'       => env('APP_URL', 'http://localhost'),"
            . "\n    'traffic_tracking'   => env('MARBLE_TRAFFIC_TRACKING', false),"
            . "\n    'api_token_lifetime' => 365,"
            . "\n];"
            . '</code></pre>'
        );

        // ── Blog ──────────────────────────────────────────────────────────────
        $blog = $this->item($blogIndex, $startpage->id, 2, null, true);
        $this->vals($blog, ['name' => 'Blog', 'slug' => 'blog',
            'intro' => 'Insights, tutorials, and news from the Marble CMS team.']);

        $post1 = $this->item($blogPost, $blog->id, 0, null, false);
        $this->vals($post1, [
            'name'         => 'Introducing Marble CMS',
            'slug'         => 'introducing-marble-cms',
            'author'       => 'The Marble Team',
            'publish_date' => '2026-01-15',
            'teaser'       => 'We built the CMS we always wanted — tree-based, developer-friendly, and built entirely on Laravel. Here\'s the story of why Marble exists.',
        ]);
        $this->html($post1, $blogPost, 'content',
            '<p>Today we\'re releasing Marble CMS — a tree-based content management system for Laravel that we\'ve been using internally for years and are finally ready to share with the world.</p>'
            . '<h2>Why we built Marble</h2>'
            . '<p>We needed a CMS for a complex client project. The content had a natural hierarchy: sections, sub-sections, nested pages, products inside categories, news inside folders. Every flat-page CMS we evaluated fought against this structure. Every tree-based CMS was either too heavyweight, too opinionated about the frontend, or not built for Laravel.</p>'
            . '<p>So we built our own.</p>'
            . '<h2>The core design decisions</h2>'
            . '<h3>Everything is an Item</h3>'
            . '<p>In Marble there is exactly one content model: <strong>Item</strong>. A homepage, a blog post, a product, a settings record, a team member — they\'re all Items. This uniformity means the same routing, permission, workflow, and API machinery works for every type of content.</p>'
            . '<h3>Blueprints, not page types</h3>'
            . '<p>What differentiates Items is their <strong>Blueprint</strong> — the field schema that defines what data the item holds. Blueprints are created in the admin UI without writing migrations or registering classes. Add a field, save, and it\'s live. Remove a field, and historical values are preserved but hidden.</p>'
            . '<h3>The tree is the URL</h3>'
            . '<p>A content item\'s URL comes directly from its position in the tree. Parent slugs concatenate to form the full path. Move an item to a different parent and its URL changes automatically (with the option to keep an alias for backward compatibility).</p>'
            . '<h2>How to get started</h2>'
            . '<pre><code>composer require marble/admin'
            . "\nphp artisan marble:install"
            . "\nphp artisan migrate"
            . "\nphp artisan db:seed"
            . '</code></pre>'
            . '<p>Then visit <code>/admin</code> and log in with <code>admin@admin</code> / <code>admin</code>. You\'ll find a fully built demo site already in the database — this very website, in fact.</p>'
            . '<p>We\'re excited to see what you build with it.</p>'
        );

        $post2 = $this->item($blogPost, $blog->id, 1, null, false);
        $this->vals($post2, [
            'name'         => 'The Blueprint System',
            'slug'         => 'the-blueprint-system',
            'author'       => 'The Marble Team',
            'publish_date' => '2026-02-10',
            'teaser'       => 'Blueprints are how you define content structures in Marble — without migrations or code. Here\'s a deep dive into fields, field types, groups, and best practices.',
        ]);
        $this->html($post2, $blogPost, 'content',
            '<p>One of the things that sets Marble apart is its Blueprint system. Instead of writing PHP migrations every time you need a new field, you open the admin UI and add it. The schema lives in the database alongside the content.</p>'
            . '<h2>What\'s in a Blueprint?</h2>'
            . '<p>A Blueprint has a name, an identifier (used in code), an icon, and a set of fields. It also has a few behaviour flags:</p>'
            . '<ul>'
            . '<li><strong>allow_children</strong> — Can items of this type have child items?</li>'
            . '<li><strong>api_public</strong> — Should items be exposed via the headless API?</li>'
            . '<li><strong>versionable</strong> — Should Marble keep a history of every save?</li>'
            . '<li><strong>schedulable</strong> — Can items have a scheduled publish and expiry date?</li>'
            . '</ul>'
            . '<h2>Field types</h2>'
            . '<p>Marble ships with ten field types covering the most common content patterns:</p>'
            . '<ul>'
            . '<li><strong>textfield</strong> — Single-line text. Use for names, slugs, titles.</li>'
            . '<li><strong>textblock</strong> — Multi-line plain text. Use for teasers, meta descriptions.</li>'
            . '<li><strong>htmlblock</strong> — Rich text with CKEditor 5. Use for body copy.</li>'
            . '<li><strong>file / files</strong> — File uploads linked to the Media Library.</li>'
            . '<li><strong>image / images</strong> — Image uploads with focal point support.</li>'
            . '<li><strong>date</strong> — A date picker. Use for publish dates, deadlines.</li>'
            . '<li><strong>repeater</strong> — A list of repeated sub-items, each with their own fields.</li>'
            . '<li><strong>relation</strong> — A link to another item in the tree.</li>'
            . '</ul>'
            . '<h2>Blueprint groups</h2>'
            . '<p>Group your blueprints into logical categories (e.g. "Blog", "Products", "System") to keep the admin sidebar organised. Groups are display-only — they don\'t affect content structure or permissions.</p>'
            . '<h2>Practical example: building a blog_post blueprint</h2>'
            . '<ol>'
            . '<li>Go to Admin → Blueprints → New Blueprint.</li>'
            . '<li>Set the name to "Blog Post" and identifier to "blog_post".</li>'
            . '<li>Add a <strong>textfield</strong> called "Name" (identifier: <code>name</code>).</li>'
            . '<li>Add a <strong>textfield</strong> called "Slug" (identifier: <code>slug</code>).</li>'
            . '<li>Add a <strong>textblock</strong> called "Teaser" (identifier: <code>teaser</code>).</li>'
            . '<li>Add an <strong>htmlblock</strong> called "Content" (identifier: <code>content</code>).</li>'
            . '<li>Add a <strong>textfield</strong> called "Author" (identifier: <code>author</code>).</li>'
            . '<li>Add a <strong>date</strong> field called "Publish Date" (identifier: <code>publish_date</code>).</li>'
            . '<li>Enable <code>api_public</code> and <code>versionable</code>.</li>'
            . '<li>Assign the "Blog Editorial" workflow.</li>'
            . '<li>Save. Create a Blog Index item, then create child items of type Blog Post.</li>'
            . '</ol>'
            . '<p>Create the view at <code>resources/views/marble-pages/blog_post.blade.php</code> and you\'re done.</p>'
        );

        $post3 = $this->item($blogPost, $blog->id, 2, null, false);
        $this->vals($post3, [
            'name'         => 'Content Workflows Explained',
            'slug'         => 'content-workflows-explained',
            'author'       => 'The Marble Team',
            'publish_date' => '2026-03-01',
            'teaser'       => 'Editorial teams need structure. Marble\'s workflow engine lets you define multi-step approval processes, assign per-step reviewers, and track every transition.',
        ]);
        $this->html($post3, $blogPost, 'content',
            '<p>When a single editor manages all the content, publishing is simple: write, review, publish. But when a team is involved — writers, editors, legal reviewers, marketing sign-off — you need a structured approval process. That\'s what Marble\'s workflow engine is for.</p>'
            . '<h2>Why workflows matter</h2>'
            . '<p>Without a workflow system, things fall through the cracks. Content goes live without proper review. No one knows who is responsible for approving what. Feedback gets lost in emails. Marble solves this by making the content state machine explicit and trackable.</p>'
            . '<h2>How to set up a workflow</h2>'
            . '<ol>'
            . '<li>Go to Admin → Workflows → New Workflow.</li>'
            . '<li>Name it (e.g. "Blog Editorial").</li>'
            . '<li>Add steps in order: "Written" → "In Review" → "Approved".</li>'
            . '<li>On the "In Review" step, enable Reject and choose "Written" as the reject target.</li>'
            . '<li>On the "Approved" step, enable Reject and choose "In Review" as the reject target.</li>'
            . '<li>Save the workflow.</li>'
            . '<li>Go to Admin → Blueprints → Blog Post → Edit, and select this workflow.</li>'
            . '</ol>'
            . '<h2>Step permissions</h2>'
            . '<p>For each workflow step you can restrict which user groups can advance an item from that step. This means:</p>'
            . '<ul>'
            . '<li>Writers advance items from the "Written" step (sending them to "In Review").</li>'
            . '<li>Only senior editors in the "Editors" group can advance from "In Review" to "Approved".</li>'
            . '<li>Only a legal team group can give final approval.</li>'
            . '</ul>'
            . '<p>This creates a clean separation of responsibilities without complex permission hierarchies.</p>'
            . '<h2>Notifications</h2>'
            . '<p>Configure each step to send notifications — in-app CMS bells, emails, or both. When a writer submits an article for review, the editors group gets notified immediately. No one needs to remember to check.</p>'
            . '<h2>Real-world example: 3-step approval</h2>'
            . '<p>A newspaper website uses Marble with this workflow on their <code>article</code> blueprint:</p>'
            . '<ol>'
            . '<li><strong>Drafted</strong> — Journalist writes the article.</li>'
            . '<li><strong>Sub-edited</strong> — Copy editor checks grammar, style, and length. Can reject back to Drafted.</li>'
            . '<li><strong>Legal clearance</strong> — Legal team checks for defamation or IP issues. Can reject back to Sub-edited.</li>'
            . '</ol>'
            . '<p>Only after legal clearance can the article be published. Every transition is logged with the user who performed it and a timestamp. If an article is rejected, the comment thread shows exactly what needs to change.</p>'
        );

        $post4 = $this->item($blogPost, $blog->id, 3, null, false);
        $this->vals($post4, [
            'name'         => 'Building Marble Plugins',
            'slug'         => 'building-marble-plugins',
            'author'       => 'The Marble Team',
            'publish_date' => '2026-04-01',
            'teaser'       => 'Marble now has a first-class plugin system. Learn how to build installable plugins that hook into the admin panel without touching any core files.',
        ]);
        $this->html($post4, $blogPost, 'content',
            '<p>Since v1.4, Marble has a first-class plugin system. Plugins are standard Laravel packages with <code>"type": "marble-plugin"</code> in their <code>composer.json</code>. They hook into the admin panel using the <code>MarbleAdmin</code> facade — no core files need to be modified.</p>'
            . '<h2>The MarbleAdmin facade</h2>'
            . '<p>All plugin integration happens from your ServiceProvider\'s <code>boot()</code> method:</p>'
            . '<pre><code>use Marble\Admin\Facades\MarbleAdmin;'
            . "\n\nMarbleAdmin::addNavItem('content', 'Orders', 'myplugin.orders', 'cart', ['myplugin.*']);"
            . "\nMarbleAdmin::addTopNavSection('shop', ["
            . "\n    'label'    => 'Shop',"
            . "\n    'icon'     => 'cart',"
            . "\n    'patterns' => ['marble.shop.*'],"
            . "\n    'items'    => ["
            . "\n        ['label' => 'Overview', 'route' => 'marble.shop.index', 'icon' => 'chart_bar'],"
            . "\n        ['label' => 'Orders',   'route' => 'marble.shop.orders', 'icon' => 'cart'],"
            . "\n    ],"
            . "\n]);"
            . "\nMarbleAdmin::addAsset('css', asset('vendor/myplugin/plugin.css'));"
            . "\nMarbleAdmin::addCkEditorPlugin('myplugin', asset('vendor/myplugin/ckeditor/'), ['MyButton']);"
            . '</code></pre>'
            . '<h2>Plugin discovery</h2>'
            . '<p>Plugins are auto-discovered via Laravel\'s standard package discovery (<code>extra.laravel.providers</code> in <code>composer.json</code>). The admin panel\'s <strong>System → Plugins</strong> page searches Packagist for all packages with <code>type: marble-plugin</code> and enriches results with the <a href="https://github.com/marblecms/plugins">community registry</a>, which marks packages as verified and featured.</p>'
            . '<h2>The ecommerce plugin</h2>'
            . '<p>The first official plugin is <strong>marblecms/marble-ecommerce</strong>: products as Marble Items, a session-based cart, Stripe Checkout, and a full Shop admin section with orders and discount codes. Install it with:</p>'
            . '<pre><code>composer require marblecms/marble-ecommerce'
            . "\nphp artisan marble:ecommerce:install"
            . '</code></pre>'
            . '<p>See the <a href="https://github.com/marblecms/plugin-ecommerce">plugin repository</a> for the full API documentation.</p>'
        );

        $post5 = $this->item($blogPost, $blog->id, 4, null, false);
        $this->vals($post5, [
            'name'         => 'Two-Factor Authentication for the Admin',
            'slug'         => 'two-factor-authentication',
            'author'       => 'The Marble Team',
            'publish_date' => '2026-04-05',
            'teaser'       => 'Marble v1.4 adds TOTP-based two-factor authentication for admin users. Enable it per user in seconds, with backup codes and full Authenticator app compatibility.',
        ]);
        $this->html($post5, $blogPost, 'content',
            '<p>Marble v1.4 ships with TOTP-based two-factor authentication for admin users. It works with any standard Authenticator app (Google Authenticator, Authy, 1Password, etc.) and is fully opt-in per user.</p>'
            . '<h2>Enabling 2FA</h2>'
            . '<ol>'
            . '<li>Go to your user profile in the admin (top-right avatar menu).</li>'
            . '<li>Scroll to the <strong>Two-Factor Authentication</strong> section and click <strong>Set up 2FA</strong>.</li>'
            . '<li>Scan the QR code with your Authenticator app.</li>'
            . '<li>Enter the 6-digit code to confirm and activate.</li>'
            . '<li>Save your backup codes somewhere safe — each can be used once if you lose access to your device.</li>'
            . '</ol>'
            . '<h2>Logging in with 2FA</h2>'
            . '<p>Once enabled, after entering your email and password you will be redirected to a second screen asking for your current 6-digit code. Enter it and you\'re in. If you\'ve lost your device, enter one of your backup codes instead.</p>'
            . '<h2>Disabling 2FA</h2>'
            . '<p>Go back to your user profile and click <strong>Disable 2FA</strong>. Admin users with the right permissions can also manage 2FA settings for other users from the Users section.</p>'
        );

        $post6 = $this->item($blogPost, $blog->id, 5, null, false);
        $this->vals($post6, [
            'name'         => 'AI Writing Assistant in the Editor',
            'slug'         => 'ai-writing-assistant',
            'author'       => 'The Marble Team',
            'publish_date' => '2026-04-10',
            'teaser'       => 'Marble v1.4 adds an AI writing assistant directly inside the CKEditor rich text editor. Connect OpenAI or Anthropic and generate, expand, or rewrite content without leaving the admin.',
        ]);
        $this->html($post6, $blogPost, 'content',
            '<p>Marble v1.4 adds an AI writing assistant to every rich text field in the admin. Click the <strong>AI Assist</strong> button in the CKEditor toolbar, describe what you want, and the result is inserted directly into the editor.</p>'
            . '<h2>Setup</h2>'
            . '<p>Go to <strong>System → Configuration</strong> and configure your AI provider:</p>'
            . '<ul>'
            . '<li><strong>Provider</strong> — OpenAI or Anthropic</li>'
            . '<li><strong>API Key</strong> — your OpenAI or Anthropic API key</li>'
            . '<li><strong>Model</strong> — leave blank for the default (<code>gpt-4o</code> / <code>claude-sonnet-4-6</code>), or specify any model your account has access to</li>'
            . '</ul>'
            . '<h2>Using the assistant</h2>'
            . '<p>With any HTML field open in the editor:</p>'
            . '<ol>'
            . '<li>Click the <strong>AI Assist</strong> button in the toolbar (sparkle icon).</li>'
            . '<li>Describe what you want — "Write an intro paragraph about our refund policy", "Expand this into a full section", "Rewrite this to be more concise".</li>'
            . '<li>The assistant sees the current editor content as context, so it can expand or rewrite existing copy.</li>'
            . '<li>Click <strong>Insert</strong> to place the result into the editor, where you can edit it further.</li>'
            . '</ol>'
            . '<p>The assistant outputs clean HTML (<code>&lt;p&gt;</code>, <code>&lt;h2&gt;</code>, <code>&lt;ul&gt;</code>, etc.) — no markdown, no code fences, ready to publish.</p>'
        );

        // ── Changelog ─────────────────────────────────────────────────────────
        $changelog = $this->item($docsSection, $startpage->id, 3, null, true);
        $this->vals($changelog, [
            'name'        => 'Changelog',
            'slug'        => 'changelog',
            'description' => 'A record of every Marble CMS release, with details of what was added, changed, and fixed.',
        ]);

        $v14 = $this->item($changelogEntry, $changelog->id, 0, null, false);
        $this->vals($v14, [
            'name'         => 'v1.4 — Plugin API, Two-Factor Auth, AI Assistant',
            'slug'         => 'v1-4',
            'version'      => '1.4',
            'release_date' => '2026-04-19',
        ]);
        $this->html($v14, $changelogEntry, 'content',
            '<h2>Plugin API</h2>'
            . '<p>Marble now has a first-class plugin system. Use the <code>MarbleAdmin</code> facade from your plugin\'s ServiceProvider to hook into the admin panel without modifying any core files. Methods: <code>addNavItem</code>, <code>addTopNavSection</code>, <code>addAsset</code>, <code>addCkEditorPlugin</code>. System → Plugins provides a searchable plugin marketplace backed by Packagist.</p>'
            . '<h2>Two-Factor Authentication</h2>'
            . '<p>TOTP-based 2FA for all admin users. Enable per user from the profile page with a QR code setup flow. Backup codes are generated on enable. Login redirects to the 2FA challenge automatically when enabled.</p>'
            . '<h2>AI Writing Assistant</h2>'
            . '<p>An AI Assist button in every CKEditor rich text toolbar. Supports OpenAI (GPT-4o) and Anthropic (Claude). Configure in System → Configuration. The assistant receives the current editor content as context and inserts clean HTML output directly into the editor.</p>'
            . '<h2>Laravel Events</h2>'
            . '<p>Marble now fires standard Laravel events: <code>ItemSaved</code>, <code>ItemPublished</code>, <code>ItemTrashed</code>, <code>PortalUserRegistered</code>, <code>PortalUserLoggedIn</code>, <code>MarbleFormSubmitted</code>. Listen from any ServiceProvider or EventServiceProvider.</p>'
            . '<h2>System → Packages</h2>'
            . '<p>The Export and Import pages have been merged into a single tabbed page under System → Packages.</p>'
            . '<h2>Other</h2>'
            . '<ul>'
            . '<li><code>BlueprintInstaller</code> helper for idempotent blueprint + field setup in plugin install commands</li>'
            . '<li>Laravel 13 support</li>'
            . '</ul>'
        );

        $v01 = $this->item($changelogEntry, $changelog->id, 1, null, false);
        $this->vals($v01, [
            'name'         => 'v0.1 — Initial Release',
            'slug'         => 'v0-1-initial-release',
            'version'      => '0.1',
            'release_date' => '2026-01-15',
        ]);
        $this->html($v01, $changelogEntry, 'content',
            '<p>The first public release of Marble CMS. This version establishes the core architecture and includes the full feature set described in the documentation.</p>'
            . '<h2>What\'s included</h2>'
            . '<ul>'
            . '<li><strong>Content tree with Blueprints and Items</strong> — Define any content structure via Blueprints. All content lives in a hierarchical item tree with materialised paths for fast queries.</li>'
            . '<li><strong>Multi-language support</strong> — Every translatable field stores one value per language. Switch languages from the admin item edit view.</li>'
            . '<li><strong>Multi-site support</strong> — Map multiple domains to different root items in the shared tree. Each site has its own settings item.</li>'
            . '<li><strong>Workflow engine</strong> — Multi-step approval workflows with per-step group restrictions, reject-with-comment, and notifications.</li>'
            . '<li><strong>Headless REST API</strong> — Expose any Blueprint as JSON. Token-authenticated. Supports filtering by blueprint and including children.</li>'
            . '<li><strong>Traffic analytics</strong> — First-party page view tracking with D3.js bar charts, session counts, and referrer flow graphs.</li>'
            . '<li><strong>A/B testing</strong> — Create content variants with configurable traffic splits. Track impressions and conversions per variant.</li>'
            . '<li><strong>Collaboration</strong> — Comments and tasks on every item for editorial team coordination.</li>'
            . '<li><strong>Portal users</strong> — Separate frontend authentication for member portals and intranets.</li>'
            . '</ul>'
        );

        // ── Screenshots ───────────────────────────────────────────────────────
        $screenshotsItem = $this->item($screenshotsPage, $startpage->id, 4, null, true);
        $this->vals($screenshotsItem, [
            'name'  => 'Screenshots',
            'slug'  => 'screenshots',
            'intro' => 'A look at the Marble CMS admin interface — from the dashboard to the blueprint editor.',
        ]);
        $this->seedScreenshots($screenshotsPage, $imagesFt);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // DEMO-SPECIFIC HELPERS
    // ════════════════════════════════════════════════════════════════════════════

    private function seedScreenshots(Blueprint $screenshotsPage, FieldType $imagesFt): void
    {
        // Already seeded guard — find the item fresh
        $item = Item::whereHas('blueprint', fn($q) => $q->where('identifier', 'screenshots_page'))
            ->latest('id')->first();
        if (!$item) return;

        $field = \Marble\Admin\Models\BlueprintField::where('blueprint_id', $screenshotsPage->id)
            ->where('identifier', 'screenshots')
            ->first();
        if (!$field) return;

        if (ItemValue::where('item_id', $item->id)->where('blueprint_field_id', $field->id)->exists()) {
            return;
        }

        $folder = MediaFolder::firstOrCreate(['name' => 'Screenshots', 'parent_id' => null]);

        $entries = [];
        foreach (range(1, 6) as $i) {
            $src = null;
            foreach ([base_path("Screenshot{$i}.png"), dirname(__DIR__, 5) . "/Screenshot{$i}.png"] as $candidate) {
                if (file_exists($candidate)) { $src = $candidate; break; }
            }
            if (!$src) continue;

            $filename = "screenshot-{$i}.png";
            Storage::put($filename, file_get_contents($src));

            [$width, $height] = @getimagesize($src) ?: [0, 0];

            $media = Media::firstOrCreate(
                ['filename' => $filename],
                [
                    'original_filename' => "Screenshot{$i}.png",
                    'disk'              => 'local',
                    'mime_type'         => 'image/png',
                    'size'              => filesize($src),
                    'width'             => $width,
                    'height'            => $height,
                    'media_folder_id'   => $folder->id,
                ]
            );

            $entries[] = [
                'media_id'          => $media->id,
                'filename'          => $media->filename,
                'original_filename' => $media->original_filename,
                'size'              => $media->size,
                'mime_type'         => $media->mime_type,
                'transformations'   => [],
            ];
        }

        if ($entries) {
            ItemValue::create([
                'item_id'            => $item->id,
                'blueprint_field_id' => $field->id,
                'language_id'        => $this->en->id,
                'value'              => json_encode($entries),
            ]);
        }
    }
}
