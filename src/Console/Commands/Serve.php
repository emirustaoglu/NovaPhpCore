<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Helper;

class Serve
{
    public function handle(): void
    {
        echo "\e[31m
  _   _                 ____  _           
 | \ | | _____   ____ _|  _ \| |__  _ __  
 |  \| |/ _ \ \ / / _` | |_) | '_ \| '_ \ 
 | |\  | (_) \ V / (_| |  __/| | | | |_) |
 |_| \_|\___/ \_/ \__,_|_|   |_| |_| .__/ 
                                   |_|    
\e[0m";
        $host = $option ?? '127.0.0.1:1108'; // Eğer bir IP verilmemişse varsayılan değer
        $command = "php -S $host -t public";
        exec($command);
        echo "[" . date("Y-m-d H:i:s") . "] PHP " . Helper::phpSurum(2) . " NovaPhp v1.0.0 Development Server (" . $host . ") started";
    }

    public static function getDescription()
    {
        return "Uygulamanızı ayağa kaldırır.";
    }
}