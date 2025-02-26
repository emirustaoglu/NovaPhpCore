<?php

namespace NovaCore\Database;

use NovaCore\Database\Schema\Schema;

abstract class Migration
{
    protected string $connection = 'default';

    abstract public function up(): void;
    abstract public function down(): void;

    public function getConnection(): string
    {
        return $this->connection;
    }

    protected function schema(): Schema
    {
        return new Schema();
    }

    protected function raw(string $sql): void
    {
        Database::getInstance($this->connection)
            ->raw($sql)
            ->execute();
    }
}