<?php

namespace NovaCore\Console\Commands;

use NovaCore\Database\MigrationManager;

class Migrate
{
    public function handle(): void
    {
        global $argv;
        $manager = new MigrationManager(BasePath . "database/migrations", array());
        if ($argv[2] == "up") {
            $manager->migrateUp();
        } else {
            $manager->migrateDown();
        }
    }

    public static function getDescription(): string
    {
        return "Veritabanı eşitlemesini yapar.";
    }
}