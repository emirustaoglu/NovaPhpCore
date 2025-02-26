<?php

namespace NovaCore\Database;

abstract class Seeder
{
    protected string $connection = 'default';

    abstract public function run(): void;

    public function getConnection(): string
    {
        return $this->connection;
    }

    protected function call(string $class): void
    {
        $seeder = new $class();
        $seeder->run();
    }
}
