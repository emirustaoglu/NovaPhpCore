<?php

namespace NovaCore\Database;

use NovaCore\Config\ConfigLoader;

class DatabaseServiceProvider
{
    public function register(): void
    {
        // Migration ve Seed yollarını ayarla
        $basePath = defined('BasePath') ? BasePath : dirname(__DIR__, 4);
        
        $paths = [
            'migrations' => $basePath . '/database/migrations',
            'seeds' => $basePath . '/database/seeds',
        ];

        // Dizinleri oluştur
        foreach ($paths as $path) {
            if (!is_dir($path)) {
                mkdir($path, 0777, true);
            }
        }

        // Migration ve Seed manager'ları başlat
        $migrationManager = new MigrationManager($paths['migrations']);
        $seedManager = new SeedManager($paths['seeds']);

        return [
            'migrationManager' => $migrationManager,
            'seedManager' => $seedManager
        ];
    }
}
