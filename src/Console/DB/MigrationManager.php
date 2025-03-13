<?php

namespace NovaPhp\Core\Console\DB;

use NovaPhp\Database\DB;

class MigrationManager
{
    protected string $migrationsTable = 'migrations';
    protected string $migrationsPath = "";

    public function __construct()
    {
        $this->migrationsPath = config('database.migrations_path');
        $this->createMigrationsTable();
    }

    protected function createMigrationsTable(): void
    {
        DB::query(" 
            CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");
    }

    public function migrate()
    {
        $files = glob($this->migrationsPath . '*.php');
        $batch = $this->getNextBatchNumber();

        foreach ($files as $file) {
            require_once $file;
            $className = pathinfo($file, PATHINFO_FILENAME);

            if ($this->isMigrationApplied($className)) {
                continue;
            }

            $migration = new $className($this->pdo);
            $migration->up();

            $this->recordMigration($className, $batch);
            echo "[Migrated] $className\n";
        }
    }

    public function rollback()
    {
        $batch = $this->getLatestBatchNumber();
        if ($batch === null) {
            echo "No migrations to rollback . \n";
            return;
        }

        $migrations = $this->getMigrationsByBatch($batch);

        foreach ($migrations as $migration) {
            require_once $this->migrationsPath . $migration . '.php';
            $className = $migration;

            $instance = new $className($this->pdo);
            $instance->down();

            $this->removeMigration($className);
            echo "[Rolled back] $className\n";
        }
    }

    protected function isMigrationApplied($migration): bool
    {
        $isMigrate = DB::table($this->migrationsTable)
            ->where('migration', '=', $migration)
            ->count();

        return (bool)$isMigrate["count"];
    }

    protected function getNextBatchNumber(): int
    {
        $nextBatch = DB::table($this->migrationsTable)
            ->select('MAX(batch) as Batch')
            ->get();
        return (int)$nextBatch["Batch"] + 1;

//        return (int)$this->pdo->query("SELECT MAX(batch) FROM {
//        $this->migrationsTable}")->fetchColumn() + 1;
    }

    protected function getLatestBatchNumber(): ?int
    {
        $latesBatch = DB::table($this->migrationsTable)
            ->select('MAX(batch) as Batch')
            ->get();
        return $latesBatch["Batch"] ? (int)$latesBatch["Batch"] : null;
    }

    protected function getMigrationsByBatch(int $batch): array
    {
        $getMigrations = DB::table($this->migrationsTable)
            ->select('migration')
            ->where('batch', '=', $batch)
            ->get();
        return $getMigrations;
    }

    protected function recordMigration(string $migration, int $batch)
    {
        DB::table($this->migrationsTable)
            ->insert(['migration' => $migration, 'batch' => $batch]);
    }

    protected function removeMigration(string $migration)
    {
        DB::table($this->migrationsTable)
            ->where('migration', '=', $migration)->delete();
    }
}