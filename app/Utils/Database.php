<?php

namespace Utils;

use Exception;
use PDO;
use PDOException;

class Database
{
    private string $dns;
    private PDO $connection;

    /**
     * Database constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $config = config('database');

        $driverConfig = $config[env('DB_CONNECTION', 'pgsql')];

        $this->dns = sprintf(
            '%s:host=%s;port=%s;dbname=%s;',
            env('DB_CONNECTION'),
            $driverConfig['host'],
            $driverConfig['port'],
            $driverConfig['database'],
        );
//        dd($this->dns);


        try {
            $this->connection = new PDO($this->dns, $driverConfig['username'], $driverConfig['password'], [
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new Exception("Connection failed: " . $e->getMessage());
        }
    }

    /**
     * @param string $sql
     * @return Database
     */
    public function query(string $sql): static
    {
        $stmt = $this->connection->prepare($sql);
        $stmt->execute();

        return $this;
    }

}
