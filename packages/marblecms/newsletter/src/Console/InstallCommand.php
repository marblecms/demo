<?php

namespace MarbleCms\Newsletter\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature   = 'marble:newsletter:install';
    protected $description = 'Install Marble Newsletter: run migrations.';

    public function handle(): int
    {
        $this->info('Running migrations…');
        $this->call('migrate');

        $this->info('');
        $this->info('Marble Newsletter installed successfully.');
        $this->info('Add to your .env:');
        $this->line('  NEWSLETTER_FROM_NAME="My Site"');
        $this->line('  NEWSLETTER_FROM_EMAIL=newsletter@example.com');
        $this->line('  NEWSLETTER_DOUBLE_OPT_IN=true');

        return self::SUCCESS;
    }
}
