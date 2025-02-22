<?php

namespace NovaCore\Console\Commands;

use NovaCore\Database\SeedsManager;

class Seeds
{
    public function handle(): void
    {
        global $argv;
        $seeds = new SeedsManager(BasePath . "database/seeds/", array());
        if ($argv[2] == "up") {
            $seeds->seedsUp();
        } else {
            $seeds->seedsDown();
        }
    }

    public static function getDescription(): string
    {
        return "Veritabanı sabit verilerinizi (seeds) çalıştırır.";
    }
}