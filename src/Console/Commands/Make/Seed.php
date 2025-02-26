<?php

namespace NovaCore\Console\Commands\Make;

use NovaCore\Console\Command;

class Seed extends Command
{
    protected string $signature = 'make:seed {seedName}';
    protected string $description = 'Yeni bir seeder dosyası oluşturur.';

    public function handle(): void
    {
        $seedName = $this->argument('seedName');
        if (!$seedName) {
            $this->error("Bir seeder adı belirtmelisiniz. Örnek: php nova make:seed UsersTableSeeder");
            return;
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Templates/Seed.php';

        // Yeni seeder dosyasının oluşturulacağı yol
        $newSeederPath = BasePath . "database/seeds/" . $seedName . '.php';

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        // Seeder sınıf adını oluştur
        $className = str_replace(' ', '', ucwords(str_replace('_', ' ', $seedName)));
        
        // Template içeriğini düzenle
        $templateContent = str_replace('%DosyaAdi%', $className, $templateContent);

        // Tablo adını otomatik belirle
        if (preg_match('/(\w+)TableSeeder/', $className, $matches)) {
            $tableName = strtolower($matches[1]);
            $templateContent = str_replace('table_name', $tableName, $templateContent);
        }

        // Dizin yoksa oluştur
        if (!file_exists(BasePath . "database/seeds/")) {
            mkdir(BasePath . "database/seeds/", 0777, true);
        }

        // Yeni seeder dosyasını oluştur
        if (file_put_contents($newSeederPath, $templateContent) !== false) {
            $this->info("Seeder dosyası oluşturuldu: $newSeederPath");
        } else {
            $this->error("Seeder dosyası oluşturulamadı!");
        }
    }
}