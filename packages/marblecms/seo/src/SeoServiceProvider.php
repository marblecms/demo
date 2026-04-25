<?php

namespace MarbleCms\Seo;

use Illuminate\Support\ServiceProvider;
use Marble\Admin\Facades\MarbleAdmin;
use Marble\Admin\Events\ItemPublished;
use Marble\Admin\Events\ItemTrashed;

class SeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/seo.php', 'seo');

        $this->app->singleton(Services\SeoService::class);
        $this->app->singleton(Services\SitemapService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'seo');

        $this->registerRoutes();
        $this->registerAdminNav();
        $this->registerComponents();
        $this->registerEventListeners();
        $this->registerPublishables();
        $this->registerCommands();
    }

    protected function registerRoutes(): void
    {
        $prefix = config('marble.route_prefix', 'admin');

        \Illuminate\Support\Facades\Route::middleware([
                'web',
                \Marble\Admin\Http\Middleware\MarbleAuthenticate::class,
                \Marble\Admin\Http\Middleware\SetMarbleGuard::class,
            ])
            ->prefix($prefix)
            ->as('marble.')
            ->group(__DIR__ . '/Http/admin_routes.php');

        \Illuminate\Support\Facades\Route::middleware(['web'])
            ->group(__DIR__ . '/Http/public_routes.php');
    }

    protected function registerAdminNav(): void
    {
        MarbleAdmin::addNavItem('structure', 'SEO', 'marble.seo.index', 'page_world', ['marble.seo.*']);
    }

    protected function registerComponents(): void
    {
        \Illuminate\Support\Facades\Blade::componentNamespace('MarbleCms\\Seo\\Components', 'seo');
    }

    protected function registerEventListeners(): void
    {
        $invalidate = function () {
            app(Services\SitemapService::class)->invalidate();
        };

        \Illuminate\Support\Facades\Event::listen(ItemPublished::class, $invalidate);
        \Illuminate\Support\Facades\Event::listen(ItemTrashed::class, $invalidate);
    }

    protected function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/seo.php' => config_path('seo.php'),
            ], 'seo-config');

            $this->publishes([
                __DIR__ . '/Resources/views' => resource_path('views/vendor/seo'),
            ], 'seo-views');
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
            ]);
        }
    }
}
