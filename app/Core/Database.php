<?php

namespace App\Core;

use Exception;
use PDO;

class Database
{
    private static ?Database $instance = null;
    private PDO $connection;
    private \PDOStatement $statement;


    /**
     * Database constructor.
     * @throws Exception
     */
    private function __construct()
    {
        $config = config('database') ?? throw new Exception('Database config not found');

        $driverConfig = $config[env('DB_CONNECTION', 'mysql')] ?? throw new Exception('Database driver config not found for ' . env('DB_CONNECTION'));

        $dns = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            env('DB_CONNECTION'),
            $driverConfig['host'],
            $driverConfig['port'],
            $driverConfig['database'],
        );

        $this->connection = new PDO($dns, $driverConfig['username'], $driverConfig['password'], [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }

        return self::$instance;
    }

    /**
     * Begin a new database transaction.
     *
     * @return void
     */
    public function beginTransaction(): void
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commit the current database transaction.
     *
     * @return void
     */
    public function commit(): void
    {
        $this->connection->commit();
    }

    /**
     * Get all tables in the database.
     *
     * @return array An array of table names.
     */
    public function getTables(): array
    {
        $connection = env('DB_CONNECTION');

        return match ($connection) {
            'mysql' => $this->connection->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN),
            'pgsql' => $this->connection->query('SELECT table_name FROM information_schema.tables WHERE table_schema = \'public\'')->fetchAll(PDO::FETCH_COLUMN),
            'sqlite' => $this->connection->query('SELECT name FROM sqlite_master WHERE type = \'table\'')->fetchAll(PDO::FETCH_COLUMN),
            default => throw new \Exception('Unsupported database connection: ' . $connection),
        };
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
        error_log($sql);

        if ($params) {
            foreach ($params as $param) {
                error_log($param);
            }
        }
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
     * @return array|bool|object An array, Object of rows as objects, or false if there are no rows.
     */
    public function findAll(): array|bool|object
    {
        return $this->statement->fetchAll(PDO::FETCH_OBJ);
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

    /**
     * Close the database connection.
     *
     * @return void
     */
    public function close(): void
    {
        $this->connection = null;
    }

    /**
     * Create a new database
     *
     * return void
     */
    public function create(): void
    {
        $dbName = env('DB_DATABASE');
        $this->connection->exec('CREATE DATABASE IF NOT EXISTS ' . $dbName);
        self::getInstance();
    }

    /**
     * Drop a database
     *
     * return void
     */
    public function drop(): void
    {
        $dbName = env('DB_DATABASE');
        $this->connection->exec('DROP DATABASE IF EXISTS ' . $dbName);
        $this->close();
    }
}
