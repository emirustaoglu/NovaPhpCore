<?php

namespace NovaCore\Console\Commands\Upload;

use NovaCore\Console\Command;
use NovaCore\Database\Database;

class StatsCommand extends Command
{
    protected string $signature = 'upload:stats';
    protected string $description = 'Depolama istatistiklerini gösterir';

    public function handle(): void
    {
        $db = new Database();

        // Genel istatistikler
        $this->showGeneralStats($db);

        // Entity bazlı istatistikler
        $this->showEntityStats($db);

        // Dosya tipi istatistikleri
        $this->showMimeTypeStats($db);
    }

    protected function showGeneralStats(Database $db): void
    {
        $this->info("\nGenel İstatistikler:");

        $stats = $db->getRow("
            SELECT 
                COUNT(*) as total_files,
                SUM(size) as total_size,
                COUNT(DISTINCT entity_id) as total_entities
            FROM uploaded_files 
            WHERE deleted_at IS NULL
        ");

        if ($stats) {
            $data = $stats['data'];
            $this->table(
                ['Metrik', 'Değer'],
                [
                    ['Toplam Dosya', number_format($data['total_files'])],
                    ['Toplam Boyut', $this->formatBytes($data['total_size'])],
                    ['Toplam Entity', number_format($data['total_entities'])]
                ]
            );
        }
    }

    protected function showEntityStats(Database $db): void
    {
        $this->info("\nEntity Bazlı İstatistikler:");

        $stats = $db->query("
            SELECT 
                entity_type,
                entity_id,
                COUNT(*) as file_count,
                SUM(size) as total_size
            FROM uploaded_files
            WHERE deleted_at IS NULL
            GROUP BY entity_type, entity_id
            ORDER BY total_size DESC
            LIMIT 10
        ");

        if ($stats && !empty($stats['data'])) {
            $rows = [];
            foreach ($stats['data'] as $row) {
                $rows[] = [
                    $row['entity_type'],
                    $row['entity_id'],
                    number_format($row['file_count']),
                    $this->formatBytes($row['total_size'])
                ];
            }

            $this->table(
                ['Tür', 'ID', 'Dosya Sayısı', 'Toplam Boyut'],
                $rows
            );
        }
    }

    protected function showMimeTypeStats(Database $db): void
    {
        $this->info("\nDosya Tipi İstatistikleri:");

        $stats = $db->query("
            SELECT 
                mime_type,
                COUNT(*) as file_count,
                SUM(size) as total_size
            FROM uploaded_files
            WHERE deleted_at IS NULL
            GROUP BY mime_type
            ORDER BY file_count DESC
        ");

        if ($stats && !empty($stats['data'])) {
            $rows = [];
            foreach ($stats['data'] as $row) {
                $rows[] = [
                    $row['mime_type'],
                    number_format($row['file_count']),
                    $this->formatBytes($row['total_size'])
                ];
            }

            $this->table(
                ['Mime Type', 'Dosya Sayısı', 'Toplam Boyut'],
                $rows
            );
        }
    }

    protected function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
    
        $bytes /= pow(1024, $pow);
    
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
