<?php

namespace MarbleCms\Seo\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature   = 'marble:seo:install';
    protected $description = 'Install Marble SEO: run migrations.';

    public function handle(): int
    {
        $this->info('Running migrations…');
        $this->call('migrate');

        $this->info('');
        $this->info('Marble SEO installed successfully.');
        $this->info('Optionally publish config:');
        $this->line('  php artisan vendor:publish --tag=seo-config');

        return self::SUCCESS;
    }
}
