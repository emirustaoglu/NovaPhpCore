<?php

namespace NovaPhp\Core\Console\Commands;

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use NovaPhp\Core\Console\Command;
use NovaPhp\Database\DB;

class Seeds extends Command
{

    protected string $signature = 'seeds:run';
    protected string $description = 'Veritabanı seeds çalıştırır';

    private string $seedTbl = 'seeds';

    public function __construct()
    {
        $this->seedTbl = config('database.seeds_table');

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
        $this->crateSeedsTable();
        DB::connect();
    }

    private function crateSeedsTable()
    {
        if (!Capsule::schema()->hasTable($this->seedTbl)) {
            Capsule::schema()->create($this->seedTbl, function (Blueprint $table) {
                $table->id()->primary(true)->autoIncrement();
                $table->string('Seeds', 250);
                $table->dateTime('EklenmeTarihi')->useCurrent();
                $table->engine('InnoDB');
            });
        }
    }

    public function handle(): void
    {

        $this->warn('Seeds dosyaları okunuyor....');
        $seedSay = 0;
        foreach (glob(app_path() . "/database/seeds/*.php") as $filename) {
            $seed = $this->apply($filename);
            $seedSay += $seed;
        }
        $this->info('Seeds işlemleri tamamlandı. Toplamda ' . $seedSay . " adet seeds dosyası başarıyla çalıştırıldı.");
    }

    private function apply($filename)
    {
        $seedName = basename($filename, ".php");
        if ($this->isSeedApplied($seedName)) {
            return 0;
        }
        try {
            $seed = include $filename;
            $seed->run();
            $this->seedInsert($seedName);
            return 1;
        } catch (\Exception $exception) {
            $this->error("Seed dosyası işlenirken bir hata meydana geldi. " . $exception->getMessage());
        }
    }

    private function isSeedApplied($seedName): bool
    {

        $isSeeds = DB::table($this->seedTbl)
            ->where('Seeds', '=', $seedName)
            ->count();

        return (bool)$isSeeds == 0 ? false : true;
    }

    private function seedInsert($seedName)
    {
        DB::query("insert into {$this->seedTbl} (Seeds) values ('$seedName')");

    }

}