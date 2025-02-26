<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Command;
use NovaCore\Database\DatabaseServiceProvider;

class MigrateRefresh extends Command
{
    protected string $signature = 'migrate:refresh {--seed} {--step=0}';
    protected string $description = 'Tüm migrationları geri alıp tekrar çalıştırır';

    public function handle(): void
    {
        $seed = $this->option('seed');
        $step = $this->option('step');

        $provider = new DatabaseServiceProvider();
        $services = $provider->register();
        $migrationManager = $services['migrationManager'];

        $this->info('Migrationlar yenileniyor...');
        $migrationManager->refresh();

        if ($seed) {
            $this->info('Seeder\'lar çalıştırılıyor...');
            $services['seedManager']->run();
        }

        $this->info('İşlem tamamlandı!');
    }
}
