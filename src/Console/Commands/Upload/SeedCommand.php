<?php

namespace NovaCore\Console\Commands\Upload;

use NovaCore\Console\Command;
use NovaCore\Database\Database;

class SeedCommand extends Command
{
    protected string $signature = 'upload:seed';
    protected string $description = 'Upload sistemi için örnek ayarlar oluşturur';

    public function handle(): void
    {
        if (!$this->confirm('Örnek ayarlar oluşturulacak. Devam etmek istiyor musunuz?')) {
            return;
        }

        $db = new Database();

        // Örnek storage ayarları
        $this->seedStorageSettings($db);

        $this->info('Örnek ayarlar başarıyla oluşturuldu!');
    }

    protected function seedStorageSettings(Database $db): void
    {
        $settings = [
            [
                'entity_type' => 'firma',
                'entity_id' => 1,
                'disk' => 'local',
                'quota_limit' => 1024 * 1024 * 1024, // 1GB
                'allowed_types' => json_encode([
                    'image/jpeg',
                    'image/png',
                    'application/pdf'
                ]),
                'max_file_size' => 5 * 1024 * 1024, // 5MB
                'is_active' => true,
                'meta' => json_encode([
                    'upload_path' => 'firmalar/{firma_id}',
                    'auto_organize' => true,
                    'create_thumbnails' => true
                ]),
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'entity_type' => 'user',
                'entity_id' => 1,
                'disk' => 'local',
                'quota_limit' => 512 * 1024 * 1024, // 512MB
                'allowed_types' => json_encode([
                    'image/jpeg',
                    'image/png'
                ]),
                'max_file_size' => 2 * 1024 * 1024, // 2MB
                'is_active' => true,
                'meta' => json_encode([
                    'upload_path' => 'users/{user_id}/photos',
                    'auto_organize' => false,
                    'create_thumbnails' => true
                ]),
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];

        foreach ($settings as $setting) {
            try {
                $db->insert('storage_settings', $setting);
            } catch (\Exception $e) {
                $this->error("Ayar eklenirken hata: " . $e->getMessage());
            }
        }
    }
}
