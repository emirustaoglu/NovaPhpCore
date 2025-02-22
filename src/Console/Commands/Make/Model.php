<?php

namespace NovaCore\Console\Commands\Make;

class Model
{
    public function handle(): void
    {
        global $argv;
        $modelName = $argv[2];
        if (!$modelName) {
            die("Bir model adı belirtmelisiniz. Örnek: php nova make:model modelName\n");
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Temp/' . 'Model.php';

        // ModelName'deki son bölümü sınıf adı olarak al
        $modelNameParts = explode('/', $modelName);
        $className = end($modelNameParts);  // Gelen son parçayı alır, örn: UserModel

        // Yeni model dosyasının oluşturulacağı yol
        $newModelPath = BasePath . "app/Model/" . $modelName . '.php';

        // Dosyanın oluşturulacağı klasör yapısını kontrol et, klasör yoksa oluştur
        $folderPath = dirname($newModelPath);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true); // Gereken klasör yapısını oluştur
        }

        if (!file_exists($templatePath)) {
            die("Template dosyası bulunamadı!\n");
        }

        // Namespace'i oluştur (App\Model\Kullanici gibi)
        $namespace = "App\Model";
        if (count($modelNameParts) > 1) {
            // className dışındaki parçaları namespace'e ekle
            $namespace .= '\\' . implode('\\', array_slice($modelNameParts, 0, -1));
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
                str_replace('%ClassArea%', "class " . $className . " { }", $templateContent)
            );

        $templateContent = str_replace(
            '%DosyaAdi%', $className,
            str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $templateContent)
        );

        // Yeni model dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newModelPath, $templateContent) !== false) {
            echo "Yeni model dosyası oluşturuldu: $newModelPath\n";
        } else {
            echo "Yeni model dosyası oluşturulamadı!\n";
        }
    }

    public static function getDescription(): string
    {
        return "Yeni bir model dosyası oluşturur. => make:model modelAdi";
    }
}