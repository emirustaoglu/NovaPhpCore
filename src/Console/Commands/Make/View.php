<?php

namespace NovaCore\Console\Commands\Make;

use NovaCore\Console\Command;

class View extends Command
{
    protected string $signature = 'make:view {viewName}';
    protected string $description = 'Yeni bir view dosyası oluşturur.';

    public function handle(): void
    {
        $viewPath = $this->argument('viewName');
        if (!$viewPath) {
            $this->error("Bir View adı belirtmelisiniz. Örnek: php nova make:view viewName");
            return;
        }

        $fileOption = explode("/", $viewPath);
        $fileOption = array_filter($fileOption, fn($v) => !empty($v));

        $folderName = "";
        $fileName = "";

        if (count($fileOption) > 1) {
            $folderName = implode("/", array_slice($fileOption, 0, -1));
            $fileName = "/" . end($fileOption);
        } else {
            $fileName = $fileOption[0];
        }

        $newViewPath = BasePath . "resources/views/" . $folderName; // Dizin yolunu alıyoruz

        // Dizin yoksa oluşturuyoruz
        if (!file_exists($newViewPath)) {
            mkdir($newViewPath, 0777, true); // Dizin ve alt dizinleri oluştur
        }

        $newViewFile = $newViewPath . $fileName . ".blade.php"; // Dosya yolunu tamamlıyoruz
        $viewData = "{{-- Dosya Adı: %DosyaAdi% --}}\n{{-- Eklenme Tarihi: %EklenmeTarihi% --}}";

        if (file_put_contents($newViewFile, str_replace('%DosyaAdi%', $fileName, str_replace('%EklenmeTarihi%', date('Y-m-d H:i:s'), $viewData))) !== false) {
            $this->info("Yeni view dosyası oluşturuldu: $newViewFile");
        } else {
            $this->error("Yeni view dosyası oluşturulamadı!");
        }
    }
}