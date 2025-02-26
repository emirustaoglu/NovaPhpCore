<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Command;
use NovaCore\Database\DatabaseServiceProvider;

class Migrate extends Command
{
    protected string $signature = 'migrate {--fresh} {--seed} {--step=}';
    protected string $description = 'Veritabanı migrationlarını çalıştırır';

    public function handle(): void
    {
        $fresh = $this->option('fresh');
        $seed = $this->option('seed');
        $step = $this->option('step');

        $provider = new DatabaseServiceProvider();
        $services = $provider->register();
        $migrationManager = $services['migrationManager'];

        if ($fresh) {
            $this->info('Veritabanı sıfırlanıyor...');
            $migrationManager->reset();
        }

        $this->info('Migrationlar çalıştırılıyor...');
        
        if ($step) {
            $migrationManager->migrate((int)$step);
        } else {
            $migrationManager->migrate();
        }

        if ($seed) {
            $this->info('Seeder\'lar çalıştırılıyor...');
            $services['seedManager']->run();
        }

        $this->info('İşlem tamamlandı!');
    }
}