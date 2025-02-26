<?php

namespace NovaCore\Console\Commands\Make;

use NovaCore\Console\Command;

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
        $newMigrationPath = BasePath . "database/migrations/" . date("Y_m_d_His") . "_" . $migrationName . '.php';

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        // Migration sınıf adını oluştur (örn: CreateUsersTable)
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));
        
        // Template içeriğini düzenle
        $templateContent = str_replace('%DosyaAdi%', $className, $templateContent);

        // Tablo adını otomatik belirle
        if (preg_match('/create_(\w+)_table/', $migrationName, $matches)) {
            $tableName = $matches[1];
            $templateContent = str_replace('table_name', $tableName, $templateContent);
        }

        // Dizin yoksa oluştur
        if (!file_exists(BasePath . "database/migrations/")) {
            mkdir(BasePath . "database/migrations/", 0777, true);
        }

        // Yeni migration dosyasını oluştur
        if (file_put_contents($newMigrationPath, $templateContent) !== false) {
            $this->info("Migration dosyası oluşturuldu: $newMigrationPath");
        } else {
            $this->error("Migration dosyası oluşturulamadı!");
        }
    }
}
