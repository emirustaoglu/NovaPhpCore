<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Command;
use NovaCore\Database\DatabaseServiceProvider;

class MigrateReset extends Command
{
    protected string $signature = 'migrate:reset';
    protected string $description = 'Tüm migrationları geri alır';

    public function handle(): void
    {
        $provider = new DatabaseServiceProvider();
        $services = $provider->register();
        $migrationManager = $services['migrationManager'];

        $this->info('Tüm migrationlar geri alınıyor...');
        $migrationManager->reset();
        $this->info('İşlem tamamlandı!');
    }
}
