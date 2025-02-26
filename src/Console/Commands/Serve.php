<?php

namespace NovaCore\Console\Commands;

use NovaCore\Console\Command;
use NovaCore\Console\Helper;

class Serve extends Command
{
    protected string $signature = 'serve';
    protected string $description = 'Uygulamanızı ayağa kaldırır.';

    public function handle(): void
    {
        $this->info("\e[31m
  _   _                 ____  _           
 | \ | | _____   ____ _|  _ \| |__  _ __  
 |  \| |/ _ \ \ / / _` | |_) | '_ \| '_ \ 
 | |\  | (_) \ V / (_| |  __/| | | | |_) |
 |_| \_|\___/ \_/ \__,_|_|   |_| |_| .__/ 
                                   |_|    
\e[0m");

        $host = $this->argument(0) ?? '127.0.0.1:1108';
        $command = "php -S $host -t public";
        
        try {
            exec($command);
            $this->info("[" . date("Y-m-d H:i:s") . "] PHP " . Helper::phpSurum(2) . " NovaPhp v1.0.0 Development Server (" . $host . ") started");
        } catch (\Exception $e) {
            $this->error("Server başlatma hatası: " . $e->getMessage());
        }
    }
}