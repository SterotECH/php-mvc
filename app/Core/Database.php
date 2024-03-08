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
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
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
     * Execute a SQL query.
     *
     * @param string $sql The SQL query to execute.
     * @param array|null $params The parameters to bind to the query.
     * @return static
     */
    public function query(string $sql, array $params = null): static
    {
        $this->statement = $this->connection->prepare($sql);
        $this->statement->execute($params);

        return $this;
    }

    /**
     * Fetch the next row from the result set.
     *
     * @return object|false The next row as an object, or false if no more rows are available.
     */
    public function find(): object|bool
    {
        return $this->statement->fetch(PDO::FETCH_OBJ);
    }

    /**
     * Fetch all rows from the result set.
     *
     * @return array|false An array of rows as objects, or false if there are no rows.
     */
    public function findAll(): array|bool
    {
        return $this->statement->fetchAll();
    }

    /**
     * Fetch the next row from the result set or return null if not found.
     *
     * @return object|null The next row as an object, or null if no more rows are available.
     */
    public function findOrFail(): ?object
    {
        $result = $this->find();
        if (!$result) {
            return null;
        }
        return $result;
    }


    /**
     * Get the number of rows affected by the last SQL statement.
     *
     * @return int The number of rows.
     */
    public function rowCount(): int
    {
        return $this->statement->rowCount();
    }

    /**
     * Get the ID of the last inserted row or sequence value.
     *
     * @param string|null $name Name of the sequence object from which the ID should be returned.
     * @return string The ID of the last inserted row.
     */
    public function lastInsertId(string $name = null): string
    {
        return $this->connection->lastInsertId($name);
    }
}
