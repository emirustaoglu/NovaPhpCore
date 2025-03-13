<?php

namespace NovaPhp\Core\Console\Commands;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use NovaPhp\Core\Console\Command;
use NovaPhp\Database\DB;

class Migrate extends Command
{

    protected string $signature = 'migrate';
    protected string $description = 'Veritabanı migrationlarını çalıştırır. {-up} tüm migration dosyalarını işler. {-down} tüm migration dosyalarınıdaki down komutunu çalıştırır.';

    private string $migrateTbl = 'migrations';

    public function __construct()
    {
        $this->migrateTbl = config('database.migrations_table');

        $connectionName = config('database.default');
        $capsule = new Capsule;

        $capsule->addConnection([
            'driver' => config("database.default"),
            'host' => config("database.connections.{$connectionName}.host"),
            'database' => config("database.connections.{$connectionName}.database"),
            'username' => config("database.connections.{$connectionName}.username"),
            'password' => config("database.connections.{$connectionName}.password"),
            'charset' => config("database.connections.{$connectionName}.charset"),
            'collation' => config("database.connections.{$connectionName}.collation"),
            'prefix' => '',
        ]);

        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        $schema = $capsule->schema();
        $this->crateMigrateTable();
        DB::connect();
    }

    private function crateMigrateTable()
    {
        if (!Capsule::schema()->hasTable($this->migrateTbl)) {
            Capsule::schema()->create($this->migrateTbl, function (Blueprint $table) {
                $table->id("Id")->primary(true)->autoIncrement();
                $table->string('Migrations', 250)->index();
                $table->dateTime('EklenmeTarihi')->useCurrent();
                $table->engine('InnoDB');
            });
        }
    }

    public function handle(): void
    {
        global $argv;
        $upDown = "up";
        if (isset($argv[2])) {
            if ($argv[2] == "-down") {
                $upDown = "down";
            } else {
                $upDown = "up";
            }
        }
        $this->warn('Migration dosyaları okunuyor....');
        $migrationSay = 0;
        foreach (glob(app_path() . "/database/migrations/*.php") as $filename) {
            $migrate = $this->apply($filename, $upDown);
            $migrationSay += $migrate;
        }
        $this->info('Migration işlemleri tamamlandı. Toplamda ' . $migrationSay . " adet migrasyon dosyası için " . $upDown . " işlemi başarıla tamamlandı.");
    }

    private function apply($filename, $direction = 'up')
    {
        $migrateName = basename($filename, ".php");
        if ($this->isMigrationApplied($migrateName) && $direction == 'up') {
            return 0;
        }
        try {
            $migration = include $filename;
            if ($migration instanceof \Illuminate\Database\Migrations\Migration) {
                if ($direction == "down") {
                    $migration->down();
                    $this->migrationDelete($migrateName);
                } else {
                    $migration->up();
                    $this->migrationInsert($migrateName);
                }
                $this->info($migrateName . " dosyası için " . $direction . " işlemi başarıyla tamamlandı...");
                return 1;
            }
            return 0;
        } catch (\Exception $exception) {
            $this->error($direction . " işlemi " . $migrateName . " dosyasında uygulanırken bir hata meydana geldi" . $exception->getMessage());
        }
    }

    private function isMigrationApplied($migrationName): bool
    {

        $isMigrate = DB::table($this->migrateTbl)
            ->where('Migrations', '=', $migrationName)
            ->count();

        return (bool)$isMigrate == 0 ? false : true;
    }

    private function migrationInsert($migrationName)
    {
        DB::query("insert into {$this->migrateTbl} (Migrations, EklenmeTarihi) values ('$migrationName', NOW())");

    }

    private function migrationDelete($migrationName)
    {
        DB::table($this->migrateTbl)
            ->where('Migrations', '=', $migrationName)
            ->delete();
    }
}