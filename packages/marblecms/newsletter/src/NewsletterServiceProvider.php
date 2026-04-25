<?php

namespace MarbleCms\Newsletter;

use Illuminate\Support\ServiceProvider;
use Marble\Admin\Facades\MarbleAdmin;

class NewsletterServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/Config/newsletter.php', 'newsletter');

        $this->app->singleton(Services\MailingService::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/Database/Migrations');
        $this->loadViewsFrom(__DIR__ . '/Resources/views', 'newsletter');

        $this->registerRoutes();
        $this->registerAdminNav();
        $this->registerComponents();
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
        MarbleAdmin::addTopNavSection('newsletter', [
            'label'    => 'Newsletter',
            'icon'     => 'email',
            'patterns' => ['marble.newsletter.*'],
            'items'    => [
                ['label' => 'Overview',    'route' => 'marble.newsletter.index',             'icon' => 'chart_bar'],
                ['label' => 'Subscribers', 'route' => 'marble.newsletter.subscribers.index', 'icon' => 'user'],
                ['label' => 'Lists',       'route' => 'marble.newsletter.lists.index',       'icon' => 'group'],
                ['label' => 'Campaigns',   'route' => 'marble.newsletter.campaigns.index',   'icon' => 'email'],
            ],
        ]);
    }

    protected function registerComponents(): void
    {
        \Illuminate\Support\Facades\Blade::componentNamespace('MarbleCms\\Newsletter\\Components', 'newsletter');
    }

    protected function registerPublishables(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/Config/newsletter.php' => config_path('newsletter.php'),
            ], 'newsletter-config');

            $this->publishes([
                __DIR__ . '/Resources/views' => resource_path('views/vendor/newsletter'),
            ], 'newsletter-views');
        }
    }

    protected function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                Console\InstallCommand::class,
                Console\SendCampaignCommand::class,
            ]);
        }
    }
}
