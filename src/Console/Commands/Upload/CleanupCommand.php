<?php

namespace NovaCore\Console\Commands\Upload;

use NovaCore\Console\Command;
use NovaCore\Database\Database;

class CleanupCommand extends Command
{
    protected string $signature = 'upload:cleanup';
    protected string $description = 'Silinmiş dosyaları temizler ve disk alanını geri kazanır';

    public function handle(): void
    {
        $db = new Database();

        // Silinmiş dosyaları bul
        $files = $db->query("
            SELECT * FROM uploaded_files 
            WHERE deleted_at IS NOT NULL 
            AND deleted_at <= DATE_SUB(NOW(), INTERVAL 30 DAY)
        ");

        if (!$files || empty($files['data'])) {
            $this->info('Temizlenecek dosya bulunamadı.');
            return;
        }

        $totalSize = 0;
        $deletedCount = 0;

        foreach ($files['data'] as $file) {
            $path = storage_path('upload/' . $file['path'] . '/' . $file['stored_name']);
            
            if (file_exists($path)) {
                // Dosyayı fiziksel olarak sil
                if (unlink($path)) {
                    $totalSize += $file['size'];
                    $deletedCount++;

                    // Veritabanından kalıcı olarak sil
                    $db->query(
                        "DELETE FROM uploaded_files WHERE id = :id",
                        ['id' => $file['id']]
                    );
                }
            }
        }

        // Boş klasörleri temizle
        $this->cleanEmptyDirectories(storage_path('upload'));

        $this->info(sprintf(
            '%d dosya temizlendi, %.2f MB alan geri kazanıldı.',
            $deletedCount,
            $totalSize / 1024 / 1024
        ));
    }

    protected function cleanEmptyDirectories(string $path): void
    {
        if (!is_dir($path)) {
            return;
        }

        $files = scandir($path);
        $files = array_diff($files, ['.', '..']);

        foreach ($files as $file) {
            $fullPath = $path . DIRECTORY_SEPARATOR . $file;
            if (is_dir($fullPath)) {
                $this->cleanEmptyDirectories($fullPath);
            }
        }

        $remainingFiles = scandir($path);
        $remainingFiles = array_diff($remainingFiles, ['.', '..']);

        if (empty($remainingFiles)) {
            rmdir($path);
        }
    }
}
