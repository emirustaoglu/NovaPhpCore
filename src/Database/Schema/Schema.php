<?php

namespace NovaCore\Database\Schema;

use NovaCore\Database\Database;

class Schema
{
    public static function create(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        Database::getInstance()->raw($sql)->execute();
    }

    public static function table(string $table, callable $callback): void
    {
        $blueprint = new Blueprint($table, true);
        $callback($blueprint);

        $sql = $blueprint->toSql();
        Database::getInstance()->raw($sql)->execute();
    }

    public static function drop(string $table): void
    {
        $sql = "DROP TABLE IF EXISTS $table";
        Database::getInstance()->raw($sql)->execute();
    }

    public static function dropIfExists(string $table): void
    {
        self::drop($table);
    }

    public static function rename(string $from, string $to): void
    {
        $sql = "RENAME TABLE $from TO $to";
        Database::getInstance()->raw($sql)->execute();
    }

    public static function hasTable(string $table): bool
    {
        $db = Database::getInstance();
        $result = $db->raw("SHOW TABLES LIKE ?", [$table])->first();
        return !empty($result);
    }

    public static function hasColumn(string $table, string $column): bool
    {
        $db = Database::getInstance();
        $result = $db->raw("SHOW COLUMNS FROM $table LIKE ?", [$column])->first();
        return !empty($result);
    }
}
