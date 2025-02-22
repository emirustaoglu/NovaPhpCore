<?php

namespace NovaCore\Console\Commands\Make;

class Middleware
{
    public function handle(): void
    {
        global $argv;
        $middlewareName = $argv[2];
        if (!$middlewareName) {
            die("Bir middleware adı belirtmelisiniz. Örnek: php nova make:middleware middlewareName\n");
        }

        // Template dosyasının yolu
        $templatePath = __DIR__ . '/../../Temp/' . 'Middlewares.php';

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
            die("Template dosyası bulunamadı!\n");
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

        // Yeni controller dosyasını oluştur ve içeriğini yaz
        if (file_put_contents($newMiddlewaresPath, $templateContent) !== false) {
            echo "Yeni middlewares dosyası oluşturuldu: $newMiddlewaresPath\n";
        } else {
            echo "Yeni middlewares dosyası oluşturulamadı!\n";
        }
    }

    public static function getDescription(): string
    {
        return "Yeni bir middlewares dosyası oluşturur. => make:middleware middlewareAdi";
    }
}