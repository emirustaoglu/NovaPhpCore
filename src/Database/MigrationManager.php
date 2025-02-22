<?php

namespace NovaCore\Database;

class MigrationManager
{
    private $migrationPath;
    private $migration;

    public function __construct($migrationPath, $settings)
    {
        $this->migrationPath = $migrationPath;
        if (!file_exists($this->migrationPath)) {
            die('Belirtilen migration dosya yolu bulunamadı.');
        }
        $this->migration = new Migration($settings['tableName'],$settings['host'], $settings['dbname'], $settings['username'], $settings['password']);
    }

    public function runMigrations($direction = 'up')
    {
        $files = scandir($this->migrationPath);
        if(count($files) < 3){ die('İşlem yapılacak migration dosyası bulunamadı.');}
        foreach ($files as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $this->migration->apply($this->migrationPath . DIRECTORY_SEPARATOR . $file, $direction);
            }
        }
    }

    public function migrateUp()
    {
        $this->runMigrations('up');
    }

    public function migrateDown()
    {
        $this->runMigrations('down');
    }
}