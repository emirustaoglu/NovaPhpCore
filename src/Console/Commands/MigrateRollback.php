<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Command;
use NovaCore\Database\DatabaseServiceProvider;

class MigrateRollback extends Command
{
    protected string $signature = 'migrate:rollback {--step=1}';
    protected string $description = 'Son migration batch\'ini geri alır';

    public function handle(): void
    {
        $step = $this->option('step');

        $provider = new DatabaseServiceProvider();
        $services = $provider->register();
        $migrationManager = $services['migrationManager'];

        $this->info('Migrationlar geri alınıyor...');
        $migrationManager->rollback((int)$step);
        $this->info('İşlem tamamlandı!');
    }
}
