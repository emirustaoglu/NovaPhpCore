<?php

namespace NovaCore\Console\Commands\Make;

class Migration
{
    public function handle(): void
    {
        global $argv;
        $migrationName = isset($argv[2]) ? $argv[2] : '';

        if (!$migrationName) {
            die("Bir migration adı belirtmelisiniz. Örnek: php nova make:migration migrationName\n");
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Temp/' . 'Migrations.php';

        // Yeni migration dosyasının oluşturulacağı yol
        $newMigrationPath = BasePath . "databases/migrations/" . date("Y-m-d-H-i-s") . "-" . $migrationName . '.php';

        if (!file_exists($templatePath)) {
            die("Template dosyası bulunamadı!\n");
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        $templateContent = str_replace('%DosyaAdi%', $migrationName, str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $templateContent));

        if (!file_exists(BasePath . "databases/migrations/")) {
            mkdir(BasePath . "databases/migrations/", 0777, true);
        }
        // Yeni migration dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newMigrationPath, $templateContent) !== false) {
            echo "Yeni migration dosyası oluşturuldu: $newMigrationPath\n";
        } else {
            echo "Yeni migration dosyası oluşturulamadı!\n";
        }
        exit;
    }

    public static function getDescription():string
    {
        return "Yeni Bir migrate dosyası oluşturur. => make:migration migrationAdi";
    }
}
