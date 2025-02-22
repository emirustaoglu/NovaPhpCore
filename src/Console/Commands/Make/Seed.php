<?php

namespace NovaCore\Console\Commands\Make;

class Seed
{
    public function handle(): void
    {
        global $argv;
        $seedName = isset($argv[2]) ? $argv[2] : '';

        if (!$seedName) {
            die("Bir seed adı belirtmelisiniz. Örnek: php nova make:seed seedName\n");
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Temp/' . 'Seeds.php';

        // Yeni seed dosyasının oluşturulacağı yol
        $newSeedPath = BasePath . "databases/seeds/" . date("Y-m-d-H-i-s") . "-" . $seedName . '.php';

        if (!file_exists($templatePath)) {
            die("Template dosyası bulunamadı!\n");
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        $templateContent = str_replace('%DosyaAdi%', $seedName, str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $templateContent));

        if (!file_exists(BasePath . "databases/seeds/")) {
            mkdir(BasePath . "databases/seeds/", 0777, true);
        }
        // Yeni seed dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newSeedPath, $templateContent) !== false) {
            echo "Yeni seeds dosyası oluşturuldu: $newSeedPath\n";
        } else {
            echo "Yeni seeds dosyası oluşturulamadı!\n";
        }
        exit;
    }

    public static function getDescription():string
    {
        return "Yeni bir seed dosyası oluşturur. => make:seed seedAdi";
    }
}