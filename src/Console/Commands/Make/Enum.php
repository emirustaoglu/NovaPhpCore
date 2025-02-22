<?php

namespace NovaCore\Console\Commands\Make;

class Enum
{
    public function handle(): void
    {
        global $argv;
        $enumName = $argv[2];
        if (!$enumName) {
            die("Bir enum adı belirtmelisiniz. Örnek: php nova make:enum enumName\n");
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Temp/' . 'Enum.php';

        // EnumName'deki son bölümü sınıf adı olarak al
        $enumNameParts = explode('/', $enumName);
        $className = end($enumNameParts);  // Gelen son parçayı alır, örn: Invoice
        if (isset($argv[3])) {
            $className .= " :" . $argv[3];
        }

        // Yeni enum dosyasının oluşturulacağı yol
        $newEnumPath = BasePath . "app/Enum/" . $enumName . '.php';

        // Dosyanın oluşturulacağı klasör yapısını kontrol et, klasör yoksa oluştur
        $folderPath = dirname($newEnumPath);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true); // Gereken klasör yapısını oluştur
        }

        if (!file_exists($templatePath)) {
            die("Template dosyası bulunamadı!\n");
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

        // Yeni controller dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newEnumPath, $templateContent) !== false) {
            echo "Yeni enum dosyası oluşturuldu: $newEnumPath\n";
        } else {
            echo "Yeni enum dosyası oluşturulamadı!\n";
        }
    }

    public static function getDescription(): string
    {
        return "Yeni bir enum dosyası oluşturur. => make:enum enumAdi";
    }
}