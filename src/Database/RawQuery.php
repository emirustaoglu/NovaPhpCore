<?php

namespace NovaCore\Database;

use PDO;

class RawQuery
{
    private PDO $pdo;
    private string $query;
    private array $bindings;

    public function __construct(PDO $pdo, string $query, array $bindings = [])
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->bindings = $bindings;
    }

    public function execute(): bool
    {
        $stmt = $this->pdo->prepare($this->query);
        return $stmt->execute($this->bindings);
    }

    public function get(): array
    {
        $stmt = $this->pdo->prepare($this->query);
        $stmt->execute($this->bindings);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function first()
    {
        $stmt = $this->pdo->prepare($this->query . ' LIMIT 1');
        $stmt->execute($this->bindings);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function value(string $column)
    {
        $result = $this->first();
        return $result[$column] ?? null;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getBindings(): array
    {
        return $this->bindings;
    }
}
