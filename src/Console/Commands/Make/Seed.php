<?php

namespace NovaPhp\Core\Console\Commands\Make;

use NovaPhp\Core\Console\Command;

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
        $newSeederPath = app_path() . "/database/seeds/" . date("Y_m_d_His") . "_". $seedName . '.php';

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        // Template içeriğini düzenle
        $templateContent = str_replace("exampleTableName", $seedName, $templateContent);


        // Dizin yoksa oluştur
        if (!file_exists(app_path() . "/database/seeds/")) {
            mkdir(app_path() . "/database/seeds/", 0777, true);
        }

        // Yeni seeder dosyasını oluştur
        if (file_put_contents($newSeederPath, $templateContent) !== false) {
            $this->info("Seeder dosyası oluşturuldu: $newSeederPath");
        } else {
            $this->error("Seeder dosyası oluşturulamadı!");
        }
    }
}