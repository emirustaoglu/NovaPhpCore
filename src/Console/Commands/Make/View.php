<?php

namespace NovaCore\Console\Commands\Make;

class View
{
    public function handle(): void
    {
        global $argv;
        $fileOption = explode("/", $argv[2]);

        $fileOption = array_filter($fileOption, fn($v) => !empty($v));

        $folderName = "";
        $fileName = "";

        if (count($fileOption) > 1) {
            $folderName = implode("/", array_slice($fileOption, 0, -1));
            $fileName = "/" . end($fileOption);
        } else {
            $fileName = $fileOption[0];
        }

        if (empty($fileName)) {
            die("Bir View adı belirtmelisiniz. Örnek: php nova make:view viewName\n");
        }

        $newViewPath = BasePath . "resources/views/" . $folderName; // Dizin yolunu alıyoruz

        // Dizin yoksa oluşturuyoruz
        if (!file_exists($newViewPath)) {
            mkdir($newViewPath, 0777, true); // Dizin ve alt dizinleri oluştur
        }

        $newViewFile = $newViewPath . $fileName . ".blade.php"; // Dosya yolunu tamamlıyoruz
        $viewData = "{{-- Dosya Adı: %DosyaAdi% --}}\n{{-- Eklenme Tarihi: %EklenmeTarihi% --}}";

        if (file_put_contents($newViewFile, str_replace('%DosyaAdi%', $fileName, str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $viewData))) !== false) {
            echo "Yeni view dosyası oluşturuldu: $newViewFile\n";
        } else {
            echo "Yeni view dosyası oluşturulamadı!\n";
        }
    }

    public static function getDescription(): string
    {
        return "Yeni bir view dosyası oluşturur. => make:view viewAdi";
    }
}