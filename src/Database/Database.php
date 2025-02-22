<?php

namespace NovaCore\Database;

class Database
{
    private $connection;

    public $data;
    private $dbHost;
    private $dbName;
    private $dbUser;
    private $dbPass;
    private $dbCharset;
    private $maintanceMode;

    public function __construct(array $config)
    {
        $this->dbHost = $config['host'];
        $this->dbName = $config['name'];
        $this->dbUser = $config['user'];
        $this->dbPass = $config['pass'];
        $this->dbCharset = $config['charset'];
        $this->maintanceMode = $config['maintanceMode'];
    }

    private function connect()
    {
        if ($this->connection === null) {
            $dsn = "mysql:host=" . $this->dbHost . ";dbname=" . $this->dbName . ";charset=" . $this->dbCharset;
            $this->connection = new PDO($dsn, $this->dbUser, $this->dbPass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        }
        return $this->connection;
    }

    private function query($query, array $params = null)
    {
        try {
            $db = $this->connect();
            $stmt = $db->prepare($query);
            if ($params !== null) {
                $stmt->execute($params);
            } else {
                $stmt->execute();
            }
            return $stmt;
        } catch (Exception $ex) {
            if ($this->maintenanceMode()) {
                print "=_" . $ex->getMessage();
                exit;
            } else {
                return 0;
            }
        }
    }

    public function getAll($query, array $params = null, $getRow = 1)
    {
        $stmt = $this->query($query, $params);
        if ($getRow == 1) {
            return ["count" => $stmt->rowCount(), "data" => $stmt->fetchAll(PDO::FETCH_ASSOC)];
        }
        return $stmt->fetchAll();
    }

    public function getRow($query, array $params = null, $getRow = 1)
    {
        $stmt = $this->query($query, $params);
        if ($getRow == 1) {
            return ["count" => $stmt->rowCount(), "data" => $stmt->fetch(PDO::FETCH_ASSOC)];
        }
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getJson($query, array $params = null)
    {
        $stmt = $this->query($query, $params);
        return json_encode($stmt->fetchAll(), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function insertUpdate($query, array $params = null)
    {
        $stmt = $this->query($query, $params);
        return $stmt->rowCount();
    }

    private function maintenanceMode()
    {
        return $this->maintanceMode;
    }
}