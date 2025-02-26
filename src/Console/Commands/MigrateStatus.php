<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Command;
use NovaCore\Database\DatabaseServiceProvider;

class MigrateStatus extends Command
{
    protected string $signature = 'migrate:status';
    protected string $description = 'Migration durumlarını gösterir';

    public function handle(): void
    {
        $provider = new DatabaseServiceProvider();
        $services = $provider->register();
        $migrationManager = $services['migrationManager'];

        $status = $migrationManager->status();

        $headers = ['Migration', 'Batch', 'Status'];
        $rows = [];

        foreach ($status as $migration) {
            $rows[] = [
                $migration['migration'],
                $migration['batch'] ?? '-',
                $migration['status']
            ];
        }

        $this->table($headers, $rows);
    }
}
