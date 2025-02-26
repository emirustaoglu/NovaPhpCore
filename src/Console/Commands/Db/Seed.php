<?php

namespace NovaCore\Console\Commands\Db;

use NovaCore\Console\Command;
use NovaCore\Database\DatabaseServiceProvider;

class Seed extends Command
{
    protected string $signature = 'db:seed {--class=}';
    protected string $description = 'Veritabanına örnek veriler ekler';

    public function handle(): void
    {
        $class = $this->option('class');

        $provider = new DatabaseServiceProvider();
        $services = $provider->register();
        $seedManager = $services['seedManager'];

        $this->info('Seeder\'lar çalıştırılıyor...');
        
        if ($class) {
            $seedManager->run([$class]);
        } else {
            $seedManager->run();
        }

        $this->info('İşlem tamamlandı!');
    }
}
