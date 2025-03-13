<?php

namespace NovaPhp\Core\Console\Commands;

use NovaPhp\Core\Console\Command;
use NovaPhp\Core\Console\Helper;

class Serve extends Command
{
    protected string $signature = 'serve';
    protected string $description = 'Uygulamanızı ayağa kaldırır.';

    public function handle(): void
    {
        global $argv;
        echo Helper::novaPhpAsci();

        $host = $argv[2] ?? '127.0.0.1:1108';
        $command = "php -S $host -t public";

        try {
            exec($command);
            $this->info("[" . date("Y-m-d H:i:s") . "] PHP " . Helper::phpSurum(2) . " NovaPhp v" . novaVersion() . " Development Server (" . $host . ") started");
        } catch (\Exception $e) {
            $this->error("Server başlatma hatası: " . $e->getMessage());
        }
    }
}