<?php

namespace NovaCore\Database;

class MigrationManager
{
    private string $migrationPath;
    private Database $db;
    private string $table;
    private array $migrations = [];

    public function __construct(string $migrationPath, string $table = 'migrations')
    {
        $this->migrationPath = $migrationPath;
        $this->table = $table;
        $this->db = Database::getInstance();
        $this->createMigrationsTable();
    }

    public function migrate(int $steps = 0): void
    {
        $this->loadMigrations();
        $pendingMigrations = $this->getPendingMigrations();

        if ($steps > 0) {
            $pendingMigrations = array_slice($pendingMigrations, 0, $steps);
        }

        $batch = $this->getNextBatchNumber();

        foreach ($pendingMigrations as $migration) {
            $this->runUp($migration, $batch);
        }
    }

    public function rollback(int $steps = 1): void
    {
        $migrations = $this->getLastBatchMigrations($steps);

        foreach ($migrations as $migration) {
            $this->runDown($migration);
        }
    }

    public function reset(): void
    {
        $migrations = array_reverse($this->getRanMigrations());

        foreach ($migrations as $migration) {
            $this->runDown($migration);
        }
    }

    public function refresh(): void
    {
        $this->reset();
        $this->migrate();
    }

    public function status(): array
    {
        $this->loadMigrations();
        $ran = $this->getRanMigrations();
        $status = [];

        foreach ($this->migrations as $name => $path) {
            $status[] = [
                'migration' => $name,
                'batch' => in_array($name, $ran) ? $this->getBatchNumber($name) : null,
                'status' => in_array($name, $ran) ? 'Ran' : 'Pending'
            ];
        }

        return $status;
    }

    private function loadMigrations(): void
    {
        $files = glob($this->migrationPath . '/*.php');
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->migrations[$name] = $file;
        }
    }

    private function createMigrationsTable(): void
    {
        if (!Schema::hasTable($this->table)) {
            Schema::create($this->table, function ($table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
                $table->timestamps();
            });
        }
    }

    private function getPendingMigrations(): array
    {
        $ran = $this->getRanMigrations();
        return array_diff(array_keys($this->migrations), $ran);
    }

    private function getRanMigrations(): array
    {
        return $this->db->table($this->table)
            ->orderBy('batch')
            ->orderBy('migration')
            ->pluck('migration')
            ->toArray();
    }

    private function getNextBatchNumber(): int
    {
        $lastBatch = $this->db->table($this->table)
            ->max('batch');

        return $lastBatch + 1;
    }

    private function getBatchNumber(string $migration): int
    {
        $result = $this->db->table($this->table)
            ->where('migration', $migration)
            ->value('batch');

        return (int) $result;
    }

    private function getLastBatchMigrations(int $steps): array
    {
        $query = $this->db->table($this->table)
            ->orderBy('batch', 'desc')
            ->orderBy('migration', 'desc');

        if ($steps > 0) {
            $query->limit($steps);
        }

        return $query->pluck('migration')->toArray();
    }

    private function runUp(string $name, int $batch): void
    {
        $file = $this->migrations[$name];
        $migration = $this->resolve($file);

        $this->db->transaction(function () use ($migration, $name, $batch) {
            $migration->up();

            $this->db->table($this->table)->insert([
                'migration' => $name,
                'batch' => $batch
            ]);

            echo "Migrated: $name\n";
        });
    }

    private function runDown(string $name): void
    {
        $file = $this->migrations[$name];
        $migration = $this->resolve($file);

        $this->db->transaction(function () use ($migration, $name) {
            $migration->down();

            $this->db->table($this->table)
                ->where('migration', $name)
                ->delete();

            echo "Rolled back: $name\n";
        });
    }

    private function resolve(string $file): Migration
    {
        require_once $file;
        $class = pathinfo($file, PATHINFO_FILENAME);
        return new $class;
    }
}