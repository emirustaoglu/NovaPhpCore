<?php

namespace NovaCore\Console\Commands\Upload;

use NovaCore\Console\Command;
use NovaCore\Database\Database;

class VerifyCommand extends Command
{
    protected string $signature = 'upload:verify';
    protected string $description = 'Dosya bütünlüğünü kontrol eder';

    public function handle(): void
    {
        $db = new Database();

        // Tüm aktif dosyaları al
        $files = $db->query("
            SELECT * FROM uploaded_files 
            WHERE deleted_at IS NULL
        ");

        if (!$files || empty($files['data'])) {
            $this->info('Kontrol edilecek dosya bulunamadı.');
            return;
        }

        $totalFiles = count($files['data']);
        $missingFiles = [];
        $corruptedFiles = [];
        $current = 0;

        foreach ($files['data'] as $file) {
            $current++;
            $this->showProgress($current, $totalFiles);

            $path = storage_path('upload/' . $file['path'] . '/' . $file['stored_name']);

            // Dosya var mı kontrol et
            if (!file_exists($path)) {
                $missingFiles[] = $file;
                continue;
            }

            // Hash kontrolü
            if ($file['hash']) {
                $currentHash = hash_file('sha256', $path);
                if ($currentHash !== $file['hash']) {
                    $corruptedFiles[] = $file;
                }
            }
        }

        $this->showResults($missingFiles, $corruptedFiles);
    }

    protected function showProgress(int $current, int $total): void
    {
        $percent = round(($current / $total) * 100);
        echo sprintf(
            "\rDosyalar kontrol ediliyor... %d/%d (%d%%)",
            $current,
            $total,
            $percent
        );
    }

    protected function showResults(array $missing, array $corrupted): void
    {
        echo "\n\n";

        if (empty($missing) && empty($corrupted)) {
            $this->info('Tüm dosyalar sağlam!');
            return;
        }

        if (!empty($missing)) {
            $this->error("\nKayıp Dosyalar:");
            foreach ($missing as $file) {
                echo sprintf(
                    "- %s/%s (ID: %d)\n",
                    $file['path'],
                    $file['stored_name'],
                    $file['id']
                );
            }
        }

        if (!empty($corrupted)) {
            $this->error("\nBozuk Dosyalar:");
            foreach ($corrupted as $file) {
                echo sprintf(
                    "- %s/%s (ID: %d)\n",
                    $file['path'],
                    $file['stored_name'],
                    $file['id']
                );
            }
        }

        // Düzeltme seçeneği sun
        if ($this->confirm("\nKayıp/bozuk dosyaları veritabanından silmek ister misiniz?")) {
            $ids = array_merge(
                array_column($missing, 'id'),
                array_column($corrupted, 'id')
            );

            $db = new Database();
            $db->query(
                "UPDATE uploaded_files SET deleted_at = NOW() WHERE id IN (" . implode(',', $ids) . ")"
            );

            $this->info('Dosyalar silindi.');
        }
    }
}
