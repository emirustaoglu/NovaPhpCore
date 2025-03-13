<?php

namespace NovaPhp\Core\Console\Commands\Make;

use NovaPhp\Core\Console\Command;

class Migration extends Command
{
    protected string $signature = 'make:migration {migrationName}';
    protected string $description = 'Yeni bir migration dosyası oluşturur.';

    public function handle(): void
    {
        $migrationName = $this->argument('migrationName');
        if (!$migrationName) {
            $this->error("Bir migration adı belirtmelisiniz. Örnek: php nova make:migration create_users_table");
            return;
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Templates/Migration.php';

        // Yeni migration dosyasının oluşturulacağı yol
        $newMigrationPath = app_path() . "/database/migrations/" . date("Y_m_d_His") . "_" . $migrationName . '.php';

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);
        $templateContent = str_replace("exampleTableName", $migrationName, $templateContent);

        // Dizin yoksa oluştur
        if (!file_exists(app_path() . "/database/migrations/")) {
            mkdir(app_path() . "/database/migrations/", 0777, true);
        }

        // Yeni migration dosyasını oluştur
        if (file_put_contents($newMigrationPath, $templateContent) !== false) {
            $this->info("Migration dosyası oluşturuldu: $newMigrationPath");
        } else {
            $this->error("Migration dosyası oluşturulamadı!");
        }
    }
}
