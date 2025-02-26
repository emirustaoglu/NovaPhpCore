<?php

namespace NovaCore\Console\Commands\Make;

use NovaCore\Console\Command;

class Controller extends Command
{
    protected string $signature = 'make:controller {controllerName}';
    protected string $description = 'Yeni bir controller dosyası oluşturur.';

    public function handle(): void
    {
        $controllerName = $this->argument('controllerName');
        if (!$controllerName) {
            $this->error("Bir controller adı belirtmelisiniz. Örnek: php nova make:controller controllerName");
            return;
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Templates/' . 'Controller.php';

        // ControllerName'deki son bölümü sınıf adı olarak al
        $controllerNameParts = explode('/', $controllerName);
        $className = end($controllerNameParts);  // Gelen son parçayı alır, örn: AuthController

        // Yeni controller dosyasının oluşturulacağı yol
        $newControllerPath = BasePath . "app/Controllers/" . $controllerName . '.php';

        // Dosyanın oluşturulacağı klasör yapısını kontrol et, klasör yoksa oluştur
        $folderPath = dirname($newControllerPath);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true); // Gereken klasör yapısını oluştur
        }

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Namespace'i oluştur (App\Controllers\Kullanici gibi)
        $namespace = "App\Controllers";
        if (count($controllerNameParts) > 1) {
            // className dışındaki parçaları namespace'e ekle
            $namespace .= '\\' . implode('\\', array_slice($controllerNameParts, 0, -1));
        }

        // Template dosyasını oku
        $templateContent = file_get_contents($templatePath);

        // Template içeriğini değiştirme
        $templateContent = "<?php \n" . str_replace(
                '%NameSpace%',
                "/*
 * Dosya Adı => %DosyaAdi%
 * Eklenme Tarihi => %EklenmeTarihi%
 *
 */

namespace " . $namespace . ";", // Dinamik namespace ekle
                str_replace(
                    '%UseArea%',
                    "use Core\Bootstrap; use Core\Controller;",
                    str_replace('%ClassArea%', "class " . $className . " { }", $templateContent)
                )
            );

        $templateContent = str_replace(
            '%DosyaAdi%', $className,
            str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $templateContent)
        );

        // Yeni controller dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newControllerPath, $templateContent) !== false) {
            $this->info("Yeni controller dosyası oluşturuldu: $newControllerPath");
        } else {
            $this->error("Yeni controller dosyası oluşturulamadı!");
        }
    }
}