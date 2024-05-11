<?php

namespace App\Traits;

use App\Core\Database;
use App\Core\Session;
use InvalidArgumentException;

trait Model
{

    protected static Database $database;
    protected static array $whereParams = [];
    protected static string $whereClause = '';

    /**
     * Save the model data to the database.
     *
     * @return object|array|null The saved record as an array, or null if not saved.
     */
    public function save(): object|array|null
    {
        $data = get_object_vars($this);

        $id = $data['id'] ?? null;
        unset($data['id']);

        $table = $this->getTableName();
        $updateData = $data;

        $params = array_values($updateData);

        if ($id) {
            $updateColumns = implode(' = ?, ', array_keys($updateData)) . ' = ?';
            array_push($params, $id);
            $sql = "UPDATE $table SET $updateColumns WHERE id = ?";
        } else {
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        }

        self::init();
        self::$database->query($sql, $params);

        return self::findById($id);
    }

    /**
     * Select all records from the database.
     *
     * @return string The SQL query to select all records.
     */
    public static function select(array $columns = null): string
    {
        $table = self::getTableName();
        $fields = $columns ? implode(', ', array_map(function ($column) use ($table) {
            if (str_contains($column, '.')) {
                return $column;
            } else {
                return "$table.$column";
            }
        }, $columns)) : implode(',', static::$fields);
        return "SELECT $fields FROM $table";
    }

    /**
     * Delete a record from the database.
     *
     * @param int|string $id The ID of the record to delete.
     * @return void
     */
    public static function delete(string|int $id): void
    {
        self::init();
        self::$database->query("DELETE FROM " . self::getTableName() . " WHERE id = ?", [$id]);
    }

    /**
     * Find a record by ID.
     *
     * @param string|int $id The ID of the record to find.
     * @param array|null $columns The columns to select.
     */
    public static function findById(string|int $id, array $columns = null)
    {

        $query = $columns ? self::select([...$columns]) : self::select();
        $query .= ' WHERE id = ?';
        self::init();
        $result = self::$database->query($query, [$id])->findOrFail();

        return $result ? $result : null;
    }

    /**
     * Get a record from the database by its slug.
     *
     * @param string $slug The slug of the record to retrieve.
     * @return object|null The retrieved record as an array, or null if not found.
     */
    public static function findBySlug(string $slug): ?object
    {
        $query = self::select();
        $query .= 'WHERE slug = ?';
        return self::$database->query($query, [$slug])->findOrFail();
    }

    public static function all(?array $fields = []): object|bool|array
    {
        self::init();
        return !empty($fields) ? self::$database->query(self::select([...$fields]))->findAll()
            : self::$database->query(self::select())->findAll();
    }

    /**
     * Find a record by a specific key-value pair.
     *
     * @param string $key The key to search for.
     * @param string $value The value to match.
     * @return object|null The found record, or null if not found.
     *
     * @example ```php
     * $user = User::find('username', 'john_doe');
     * if ($user) {
     *     echo "User found: " . $user->username;
     * } else {
     *     echo "User not found.";
     * }
     *
     */
    public static function find(string $key, string $value, array $columNames = null): ?object
    {
        self::init();
        $sql = $columNames ? self::select([$key, ...$columNames]) : self::select([$key]);
        $sql .= " WHERE $key = ?";
        $result = self::$database->query($sql, [$value])->find();

        return $result ?: null;
    }

    /**
     * Select distinct records from the database.
     *
     * @param array|null $fields The fields to select. If null, select all fields.
     * @return array|bool|object An object representing the query.
     */
    public static function distinct(?array $fields = null): array|bool|object
    {
        $table = self::getTableName();
        $fieldsStr = $fields ? implode(', ', $fields) : implode(', ', static::$fields);
        $query = "SELECT DISTINCT $fieldsStr FROM $table";
        return self::$database->query($query)->findAll();
    }

    /**
     * Count the total number of records in the table.
     *
     * @return int The total number of records.
     */
    public static function count(): int
    {
        $table = self::getTableName();
        $query = "SELECT COUNT(*) AS count FROM $table";
        self::$lastQuery = $query;
        return (int)self::$database->query($query)->find();
    }

    /**
     * Get records from the database based on conditions and select specific fields.
     *
     * @param array|null $fields The fields to select.
     * @return array|object An array of objects representing the retrieved records.
     */
    public function get(?array $fields = null): array|object
    {
        $query = $this->buildQuery($fields);
        return $this->executeQuery($query, true);
    }

    public function first(?array $fields = null): array|object|bool
    {
        $query = $this->buildQuery($fields);
        $query .= ' LIMIT 1';
        return $this->executeQuery($query, false);
    }

    public function last(?array $fields = null): array|object|bool
    {
        $query = $this->buildQuery($fields);
        $query .= ' ORDER BY id DESC LIMIT 1';
        return $this->executeQuery($query, false);
    }

    private function buildQuery(?array $fields = null): string
    {
        $query = $fields ? self::select([...$fields]) : self::select();

        if (self::$whereClause) {
            $query .= ' WHERE' . self::$whereClause;
            // self::$whereClause = '';
            // self::$whereParams  = [];
        }
        return $query;
    }

    private function executeQuery(string $query, bool $findAll): array|object|bool
    {
        self::init();
        if (self::$whereParams) {
            $params = self::$whereParams;
            return $findAll
                ? self::$database->query($query, $params)->findAll()
                : self::$database->query($query, $params)->find();
        } else {
            return $findAll
                ? self::$database->query($query)->findAll()
                : self::$database->query($query)->find();
        }
    }

    /**
     * Add a WHERE clause to the query.
     *
     * @param array $conditions An associative array of column-value pairs for the conditions.
     * @param string $operator The operator to use for comparisons (e.g., '=', '>', 'LIKE').
     * @param string $conjunction The conjunction to use between conditions (e.g., 'AND', 'OR').
     * @return \App\Models\Model|Model An object representing the modified query.
     */
    public static function where(array $conditions, string $operator = '=', string $conjunction = 'AND'): self
    {
        $validOperators = ['=', '<', '>', '<=', '>=', '<>', '!=', 'LIKE', 'NOT LIKE', 'IN', 'NOT IN'];
        if (!in_array($operator, $validOperators)) {
            throw new InvalidArgumentException("Invalid operator: $operator");
        }

        $whereStr = '';
        foreach ($conditions as $column => $value) {
            if (is_array($value)) {
                if ($operator === 'IN' || $operator === 'NOT IN') {
                    $placeholders = implode(', ', array_fill(0, count($value), "?"));
                    $whereStr .= "$column $operator ($placeholders) $conjunction ";
                    self::$whereParams = array_merge(self::$whereParams, $value);
                } else {
                    throw new InvalidArgumentException("Invalid operator '$operator' for array value");
                }
            } else {
                $whereStr .= " $column $operator :$column $conjunction ";
                self::$whereParams[":$column"] = $value;
            }
        }
        $whereStr = rtrim($whereStr, "$conjunction ");
        self::$whereClause .= " $whereStr";
        return new static();
    }


    /**
     * Add a JOIN clause to the query.
     *
     * @param string $table The table to join.
     * @param string $condition The join condition.
     * @param string $type The type of join (e.g., 'INNER', 'LEFT', 'RIGHT').
     * @return \App\Models\Model|Model An object representing the modified query.
     */
    public static function join(string $table, string $condition, string $type = 'INNER'): self
    {
        self::$joinClause .= " $type JOIN $table ON $condition";
        return new static();
    }


    /**
     * Get the JOIN clause string.
     *
     * @return string The JOIN clause string.
     */
    protected function getJoinClause(): string
    {
        return self::$joinClause;
    }

    /**
     * Get the JOIN clause parameters.
     *
     * @return array The JOIN clause parameters.
     */
    protected function getJoinParams(): array
    {
        return self::$joinParams;
    }

    /**
     * Perform a raw query.
     *
     * @param string $query The raw SQL query.
     * @param array|null $params The parameters to bind to the query.
     * @return array|object|bool|null An array of objects representing the retrieved records.
     */
    public static function raw(string $query, array $params = null): array|object|bool|null
    {
        self::init();
        return $params ? self::$database->query($query, $params)->findAll() : self::$database->query($query)->findAll();
    }

    protected function getWhereClause(): string
    {
        return self::$whereClause;
    }

    protected function getWhereParams(): array
    {
        return self::$whereParams;
    }

    public function __destruct()
    {
        self::$whereClause = '';
        self::$whereParams = [];
    }
}
