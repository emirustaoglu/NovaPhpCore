<?php

use NovaCore\Database\Seeder;
use NovaCore\Database\Database;

class %DosyaAdi% extends Seeder
{
    public function run(): void
    {
        $db = Database::getInstance();
        
        // Örnek veri ekleme
        $db->table('table_name')->insert([
            [
                'column1' => 'value1',
                'column2' => 'value2',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]
        ]);
    }
}
