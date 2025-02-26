<?php

namespace NovaCore\Database;

class SeedManager
{
    private string $seedPath;
    private Database $db;
    private array $seeds = [];

    public function __construct(string $seedPath)
    {
        $this->seedPath = $seedPath;
        $this->db = Database::getInstance();
    }

    public function run(array $classes = []): void
    {
        $this->loadSeeds();

        if (empty($classes)) {
            $classes = array_keys($this->seeds);
        }

        foreach ($classes as $class) {
            if (isset($this->seeds[$class])) {
                $this->runSeed($class);
            }
        }
    }

    private function loadSeeds(): void
    {
        $files = glob($this->seedPath . '/*.php');
        foreach ($files as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $this->seeds[$name] = $file;
        }
    }

    private function runSeed(string $name): void
    {
        $file = $this->seeds[$name];
        $seeder = $this->resolve($file);

        $this->db->transaction(function () use ($seeder, $name) {
            $seeder->run();
            echo "Seeded: $name\n";
        });
    }

    private function resolve(string $file): Seeder
    {
        require_once $file;
        $class = pathinfo($file, PATHINFO_FILENAME);
        return new $class;
    }
}
