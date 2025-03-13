<?php

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {
    public function up(): void
    {
        Capsule::schema()->create('exampleTableName', function (Blueprint $table) {
            $table->string("Id", 36)->primary(true)->index();
            $table->dateTime("EklenmeTarihi")->useCurrent();
            $table->string("EkleyenKullanici", 36);
            $table->dateTime("GuncellemeTarihi")->nullable();
            $table->string("GuncelleyenKullanici", 36)->nullable();
        });
    }

    public function down(): void
    {
        Capsule::schema()->dropIfExists('exampleTableName');
    }
};
