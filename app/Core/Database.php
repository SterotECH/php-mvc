<?php

namespace App\Core;

use Exception;
use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private $statement;

    /**
     * Database constructor.
     * @throws Exception
     */
    private function __construct()
    {
        $config = config('database');

        $driverConfig = $config[env('DB_CONNECTION', 'pgsql')];

        $dns = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            env('DB_CONNECTION'),
            $driverConfig['host'],
            $driverConfig['port'],
            $driverConfig['database'],
        );

        try {
            $this->connection = new PDO($dns, $driverConfig['username'], $driverConfig['password'], [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            exit('Something bad happened');
        }
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param array|null $hiddenColumns
     * @return Database
     */
    public function query(string $sql, array $params = null, array $hiddenColumns = null): static
    {
        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute($params);

        return $this;
    }


    public function findOrFail(): mixed
    {
        $result = $this->find();
        if (!$result) {
            abort();
        }

        return $result;
    }


    public function find()
    {
        return $this->statement->fetch();
    }


    public function findAll()
    {
        return $this->statement->fetchAll();
    }

    public function rowCount(): int
    {
        return count($this->findAll());
    }

    public function lastInsertId(string $name = null): string
    {
        return $this->connection->lastInsertId($name);
    }
}
