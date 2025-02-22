<?php

namespace NovaCore\Database;

use PDO;

class Migration
{
    private $pdo;
    private $tableName;
    private $host;
    private $dbName;
    private $username;
    private $password;

    public function __construct($tableName, $host, $dbName, $username, $password)
    {
        $this->tableName = $tableName;
        $this->host = $host;
        $this->dbName = $dbName;
        $this->username = $username;
        $this->password = $password;
        $this->pdo = $this->connect();
        $this->createMigrationsTable();
    }

    private function connect()
    {
        $host = $this->host; // Veritabanı sunucu adresi
        $dbName = $this->dbName; // Veritabanı adı
        $username = $this->username; // Veritabanı kullanıcı adı
        $password = $this->password; // Veritabanı şifresi
        $dsn = "mysql:host=$host;dbname=$dbName;charset=utf8mb4";

        try {
            $pdo = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
            return $pdo;
        } catch (PDOException $e) {
            die("Veritabanı bağlantı hatası: " . $e->getMessage());
        }
    }

    private function createMigrationsTable()
    {
        $query = "CREATE TABLE IF NOT EXISTS $this->tableName (
            Id INT AUTO_INCREMENT PRIMARY KEY,
            Migrations VARCHAR(250) NOT NULL,
            EklenmeTarihi DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=INNODB;";
        $this->pdo->exec($query);
    }

    public function apply($file, $direction = 'up')
    {
        $migrationName = basename($file, '.php');

        if ($this->isMigrationApplied($migrationName) && $direction === 'up') {
            echo "$migrationName migration zaten uygulanmış.\n";
            return;
        }

        $migration = include $file;

        if (!isset($migration[$direction])) {
            echo "$migrationName migration dosyasında '$direction' kısmı bulunamadı.\n";
            return;
        }

        try {
            $this->pdo->beginTransaction();
            foreach ($migration[$direction] as $query) {
                if (trim($query)) { // Boş satırları geç
                    $this->pdo->exec($query);
                }
            }

            if ($direction === 'up') {
                $this->pdo->exec("INSERT INTO $this->tableName (Migrations) VALUES ('$migrationName')");
            } elseif ($direction === 'down') {
                $this->pdo->exec("DELETE FROM $this->tableName WHERE Migrations = '$migrationName'");
            }
            if ($this->pdo->inTransaction()) {
                $this->pdo->commit();
            }
            echo "$migrationName migration '$direction' işlemi başarıyla tamamlandı.\n";
        } catch (PDOException $e) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            die("Migration '$direction' işlemi hatası: " . $e->getMessage());
        }
    }

    private function isMigrationApplied($migrationName)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM $this->tableName WHERE Migrations = ?");
        $stmt->execute([$migrationName]);
        return $stmt->fetch() !== false;
    }
}