<?php

namespace NovaCore\Console\Commands\Upload;

use NovaCore\Console\Command;
use NovaCore\Database\Database;

class InstallCommand extends Command
{
    protected string $signature = 'upload:install';
    protected string $description = 'Upload sistemi için gerekli tabloları oluşturur';

    public function handle(): void
    {
        $this->info('Upload sistemi kuruluyor...');

        // Migrations klasörünü oluştur
        $migrationsPath = database_path('migrations');
        if (!is_dir($migrationsPath)) {
            mkdir($migrationsPath, 0777, true);
        }

        // Migration dosyalarını oluştur
        $this->createMigrationFiles();

        // Migrationları çalıştır
        $this->runMigrations();

        $this->info('Upload sistemi başarıyla kuruldu!');
    }

    protected function createMigrationFiles(): void
    {
        // uploaded_files tablosu
        $this->createMigration(
            'create_uploaded_files_table',
            $this->getUploadedFilesMigration()
        );

        // storage_settings tablosu
        $this->createMigration(
            'create_storage_settings_table',
            $this->getStorageSettingsMigration()
        );
    }

    protected function createMigration(string $name, string $content): void
    {
        $filename = date('Y_m_d_His_') . $name . '.php';
        $path = database_path('migrations/' . $filename);

        file_put_contents($path, $content);
        $this->info("Migration oluşturuldu: {$filename}");
    }

    protected function getUploadedFilesMigration(): string
    {
        return <<<PHP
<?php

use NovaCore\Database\Migration;
use NovaCore\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        \$this->schema->create('uploaded_files', function (Blueprint \$table) {
            \$table->id();
            \$table->string('entity_type', 50)->nullable();  // firma, user vb.
            \$table->unsignedBigInteger('entity_id')->nullable();
            \$table->string('disk', 20)->default('local');   // local, s3 vb.
            \$table->string('original_name');
            \$table->string('stored_name');
            \$table->string('mime_type');
            \$table->unsignedBigInteger('size');
            \$table->string('path');
            \$table->string('extension', 20);
            \$table->string('hash')->nullable();
            \$table->json('meta')->nullable();
            \$table->boolean('is_public')->default(false);
            \$table->timestamp('created_at');
            \$table->timestamp('updated_at')->nullable();
            \$table->softDeletes();

            \$table->index(['entity_type', 'entity_id']);
            \$table->index('disk');
            \$table->index('mime_type');
            \$table->index('hash');
        });
    }

    public function down(): void
    {
        \$this->schema->dropIfExists('uploaded_files');
    }
};
PHP;
    }

    protected function getStorageSettingsMigration(): string
    {
        return <<<PHP
<?php

use NovaCore\Database\Migration;
use NovaCore\Database\Schema\Blueprint;

return new class extends Migration
{
    public function up(): void
    {
        \$this->schema->create('storage_settings', function (Blueprint \$table) {
            \$table->id();
            \$table->string('entity_type', 50);  // firma, user vb.
            \$table->unsignedBigInteger('entity_id');
            \$table->string('disk', 20)->default('local');
            \$table->unsignedBigInteger('quota_limit')->default(0);  // 0 = sınırsız
            \$table->json('allowed_types')->nullable();
            \$table->unsignedInteger('max_file_size')->nullable();
            \$table->boolean('is_active')->default(true);
            \$table->json('meta')->nullable();
            \$table->timestamp('created_at');
            \$table->timestamp('updated_at')->nullable();

            \$table->unique(['entity_type', 'entity_id', 'disk']);
            \$table->index('is_active');
        });
    }

    public function down(): void
    {
        \$this->schema->dropIfExists('storage_settings');
    }
};
PHP;
    }

    protected function runMigrations(): void
    {
        $db = new Database();
        
        // Migration tablosunu kontrol et
        if (!$db->hasTable('migrations')) {
            $this->createMigrationsTable($db);
        }

        // Migrationları çalıştır
        $migrations = glob(database_path('migrations/*.php'));
        foreach ($migrations as $migration) {
            $name = basename($migration);
            
            // Migration daha önce çalıştırılmış mı kontrol et
            $exists = $db->getRow(
                "SELECT * FROM migrations WHERE migration = :migration",
                ['migration' => $name]
            );

            if (!$exists) {
                require_once $migration;
                $instance = new \NovaCore\Database\Migration($db);
                $instance->up();

                // Migration kaydını ekle
                $db->insert('migrations', [
                    'migration' => $name,
                    'batch' => 1
                ]);

                $this->info("Migration çalıştırıldı: {$name}");
            }
        }
    }

    protected function createMigrationsTable(Database $db): void
    {
        $db->query("
            CREATE TABLE migrations (
                id INT AUTO_INCREMENT PRIMARY KEY,
                migration VARCHAR(255) NOT NULL,
                batch INT NOT NULL
            )
        ");
    }
}
