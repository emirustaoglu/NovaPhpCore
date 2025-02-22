<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Helper;

class Serve
{
    public function handle(): void
    {
        $host = $option ?? '127.0.0.1:1108'; // Eğer bir IP verilmemişse varsayılan değer
        $command = "php -S $host -t public";
        exec($command);
        echo "[" . date("Y-m-d H:i:s") . "] PHP " . Helper::phpSurum(2) . " Development Server (" . $host . ") started";
    }

    public static function getDescription()
    {
        return "Uygulamanızı ayağa kaldırır.";
    }
}