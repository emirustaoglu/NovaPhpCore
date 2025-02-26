<?php

use NovaCore\Database\Migration;
use NovaCore\Database\Schema\Schema;

class %DosyaAdi% extends Migration
{
    public function up(): void
    {
        Schema::create('table_name', function ($table) {
            $table->id();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('table_name');
    }
}
