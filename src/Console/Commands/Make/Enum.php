<?php

namespace NovaCore\Console\Commands\Make;

use NovaCore\Console\Command;

class Enum extends Command
{
    protected string $signature = 'make:enum {enumName}';
    protected string $description = 'Yeni bir enum dosyası oluşturur.';

    public function handle(): void
    {
        $enumName = $this->argument('enumName');
        $type = $this->argument(1);

        if (!$enumName) {
            $this->error("Bir enum adı belirtmelisiniz. Örnek: php nova make:enum enumName");
            return;
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Templates/' . 'Enum.php';

        // EnumName'deki son bölümü sınıf adı olarak al
        $enumNameParts = explode('/', $enumName);
        $className = end($enumNameParts);  // Gelen son parçayı alır, örn: Invoice
        
        if ($type) {
            $className .= " :" . $type;
        }

        // Yeni enum dosyasının oluşturulacağı yol
        $newEnumPath = BasePath . "app/Enum/" . $enumName . '.php';

        // Dosyanın oluşturulacağı klasör yapısını kontrol et, klasör yoksa oluştur
        $folderPath = dirname($newEnumPath);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true); // Gereken klasör yapısını oluştur
        }

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Namespace'i oluştur (App\Enum\Kullanici gibi)
        $namespace = "App\Enum";
        if (count($enumNameParts) > 1) {
            // className dışındaki parçaları namespace'e ekle
            $namespace .= '\\' . implode('\\', array_slice($enumNameParts, 0, -1));
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        // Template içeriğini değiştirme
        $templateContent = "<?php \n" . str_replace(
                '%NameSpace%',
                "/*
 * Dosya Adı => %DosyaAdi%
 * Eklenme Tarihi => %EklenmeTarihi%
 */

namespace " . $namespace . ";", // Dinamik namespace ekle
                str_replace('%EnumArea%', "enum " . $className . " { }", $templateContent)
            );

        $templateContent = str_replace(
            '%DosyaAdi%', $className,
            str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $templateContent)
        );

        // Yeni enum dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newEnumPath, $templateContent) !== false) {
            $this->info("Yeni enum dosyası oluşturuldu: $newEnumPath");
        } else {
            $this->error("Yeni enum dosyası oluşturulamadı!");
        }
    }
}