<?php

namespace NovaCore\Database;

use NovaCore\Config\ConfigLoader;
use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private ?PDO $dbConnection = null;
    private string $connectionName;
    private array $queryLog = [];
    private bool $logging = false;

    private function __construct(string $connectionName = null)
    {
        $this->connectionName = $connectionName ?? ConfigLoader::getInstance()->get('database.default');
    }

    public static function getInstance(string $connectionName = null): self
    {
        if (self::$instance === null) {
            self::$instance = new self($connectionName);
        }
        return self::$instance;
    }

    public function connect(): PDO
    {
        if ($this->dbConnection !== null) {
            return $this->dbConnection;
        }

        $config = ConfigLoader::getInstance()->get("database.connections.{$this->connectionName}");
        
        if (empty($config)) {
            throw new \InvalidArgumentException("Database connection '{$this->connectionName}' not configured");
        }

        if ($config['maintanceMode'] ?? false) {
            throw new \RuntimeException("Database is in maintenance mode");
        }

        try {
            $dsn = sprintf(
                "%s:host=%s;port=%d;dbname=%s;charset=%s",
                $config['driver'],
                $config['host'],
                $config['port'],
                $config['database'],
                $config['charset']
            );

            $this->dbConnection = new PDO(
                $dsn,
                $config['username'],
                $config['password'],
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );

            return $this->dbConnection;
        } catch (PDOException $e) {
            throw new \RuntimeException("Could not connect to database: " . $e->getMessage());
        }
    }

    public function getConnection(): PDO
    {
        return $this->connect();
    }

    public function table(string $table): QueryBuilder
    {
        return new QueryBuilder($this->connect(), $table);
    }

    public function raw(string $query, array $params = []): RawQuery
    {
        return new RawQuery($this->connect(), $query, $params);
    }

    public function beginTransaction(): bool
    {
        return $this->connect()->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connect()->commit();
    }

    public function rollBack(): bool
    {
        return $this->connect()->rollBack();
    }

    public function transaction(callable $callback)
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Exception $e) {
            $this->rollBack();
            throw $e;
        }
    }

    public function enableQueryLog(): void
    {
        $this->logging = true;
    }

    public function disableQueryLog(): void
    {
        $this->logging = false;
    }

    public function getQueryLog(): array
    {
        return $this->queryLog;
    }

    public function logQuery(string $query, array $bindings = [], float $time = null): void
    {
        if ($this->logging) {
            $this->queryLog[] = [
                'query' => $query,
                'bindings' => $bindings,
                'time' => $time
            ];
        }
    }
}