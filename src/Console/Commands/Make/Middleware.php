<?php

namespace NovaCore\Console\Commands\Make;

use NovaCore\Console\Command;

class Middleware extends Command
{
    protected string $signature = 'make:middleware {middlewareName}';
    protected string $description = 'Yeni bir middleware dosyası oluşturur.';

    public function handle(): void
    {
        $middlewareName = $this->argument('middlewareName');
        if (!$middlewareName) {
            $this->error("Bir middleware adı belirtmelisiniz. Örnek: php nova make:middleware middlewareName");
            return;
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Templates/' . 'Middlewares.php';

        // MiddlewaresName'deki son bölümü sınıf adı olarak al
        $middlewareNameParts = explode('/', $middlewareName);
        $className = end($middlewareNameParts);  // Gelen son parçayı alır, örn: AuthMiddleware

        // Yeni middleware dosyasının oluşturulacağı yol
        $newMiddlewaresPath = BasePath . "app/Middlewares/" . $middlewareName . '.php';

        // Dosyanın oluşturulacağı klasör yapısını kontrol et, klasör yoksa oluştur
        $folderPath = dirname($newMiddlewaresPath);
        if (!is_dir($folderPath)) {
            mkdir($folderPath, 0777, true); // Gereken klasör yapısını oluştur
        }

        if (!file_exists($templatePath)) {
            $this->error("Template dosyası bulunamadı!");
            return;
        }

        // Namespace'i oluştur (App\Controllers\Kullanici gibi)
        $namespace = "App\Middlewares";
        if (count($middlewareNameParts) > 1) {
            // className dışındaki parçaları namespace'e ekle
            $namespace .= '\\' . implode('\\', array_slice($middlewareNameParts, 0, -1));
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
                    "use Core\Bootstrap;",
                    str_replace('%ClassArea%', "class " . $className . " { }", $templateContent)
                )
            );

        $templateContent = str_replace(
            '%DosyaAdi%', $className,
            str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $templateContent)
        );

        // Yeni middleware dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newMiddlewaresPath, $templateContent) !== false) {
            $this->info("Yeni middleware dosyası oluşturuldu: $newMiddlewaresPath");
        } else {
            $this->error("Yeni middleware dosyası oluşturulamadı!");
        }
    }
}