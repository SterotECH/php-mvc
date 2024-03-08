<?php

namespace App\Models;

use App\Core\Database;
use InvalidArgumentException;
use PDO;

class Model
{
    protected static Database $database;


    /**
     * Initialize the database connection.
     */
    public static function init(): void
    {
        self::$database = Database::getInstance();
    }


    /**
     * Get all records from the database table.
     *
     * @return object|array|null
     *
     * @example
     * ```php
     * // Example usage:
     * $users = User::all();
     * foreach ($users as $user) {
     *     echo $user->username;
     * }
     * ```
     */
    public static function all(): object|array|null
    {
        return self::fetchAll("SELECT " . implode(', ', static::$fields) . " FROM " . static::$table);
    }

    /**
     * Create a new record in the database.
     *
     * @param array $data The data to insert.
     * @return object|null The created object, or null if the record was not created.
     *
     * @example
     * ```php
     * $data = ['username' => 'john_doe', 'email' => 'john@example.com'];
     * $created = User::create($data);
     * if ($created) {
     *     echo "User created successfully!";
     * } else {
     *     echo "Failed to create user.";
     * }
     * ```
     */
    public static function create(array $data): ?object
    {
        self::init();
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($values)";
        $params = array_values($data);

        self::$database->query($sql, $params);

        // Fetch the last inserted ID
        $id = self::$database->lastInsertId();

        // Return the created object
        return self::getById($id);
    }


    /**
     * Fetch all records from the database that match the specified conditions using the given conjunction.
     *
     * @param array $conditions The conditions to match records against.
     * @param string $conjunction The conjunction to use between conditions ('AND' or 'OR'). Default is 'AND'.
     * @return array|object|null An array of records that match the specified conditions.
     *
     * @example
     * ```php
     * // Example usage with 'AND' conjunction
     * $result = Model::where(['status' => 'active', 'category_id' => 1], 'AND');
     *
     * // Example usage with 'OR' conjunction
     * $result = Model::where(['status' => 'active', 'category_id' => 1], 'OR');
     * ```
     */
    public static function where(array $conditions, string $conjunction = 'AND'): array|object|null
    {
        $fields = implode(', ', static::$fields);
        $sql = "SELECT $fields FROM " . static::$table . " WHERE ";
        $sql .= implode(" $conjunction ", array_map(fn($key) => "$key = ?", array_keys($conditions)));
        $params = array_values($conditions);
        return self::fetchAll($sql, $params);
    }


    /**
     * Fetch all records from the database for the specified columns.
     *
     * @param array $columns The columns to fetch from the database.
     * @return array An array of records containing the specified columns.
     *
     * @example
     * ```php
     * // Example usage:
     * $columns = ['id', 'username', 'email'];
     * $users = User::get($columns);
     * foreach ($users as $user) {
     *     echo $user->username;
     * }
     * ```
     */
    public static function get(array $columns): array
    {
        return self::fetchAll("SELECT " . implode(', ', $columns) . " FROM " . static::$table);
    }


    /**
     * Fetch all distinct values for a specific column from the database.
     *
     * @param string $column The column for which to fetch distinct values.
     * @return array An array of distinct values for the specified column.
     *
     * @example
     * ```php
     * // Example usage:
     * $distinctEmails = User::distinct('email');
     * foreach ($distinctEmails as $email) {
     *     echo $email;
     * }
     * ```
     */
    public static function distinct(string $column): array
    {
        return self::fetchAll("SELECT DISTINCT $column FROM " . static::$table);
    }


    /**
     * Find a record by a specific key-value pair.
     *
     * @param string $key The key to search for.
     * @param string $value The value to match.
     * @return object|null The found record, or null if not found.
     *
     * @example
     * ```php
     * $user = User::find('username', 'john_doe');
     * if ($user) {
     *     echo "User found: " . $user->username;
     * } else {
     *     echo "User not found.";
     * }
     * ```
     */
    public static function find(string $key, string $value): ?object
    {
        self::init();
        $result = self::$database->query("SELECT $key FROM " . static::$table . " WHERE $key = :value", [
            'value' => $value
        ])->find();

        return $result ?: null;
    }


    /**
     * Fetch all records from the database based on the provided SQL query.
     *
     * @param string $sql The SQL query to fetch records.
     * @param array|null $params Optional parameters for the query.
     * @return array|object|null An array of records retrieved from the database.
     *
     * @api
     * ```php
     * $records = Model::fetchAll("SELECT * FROM table");
     * foreach ($records as $record) {
     *     echo $record->id . ": " . $record->name;
     * }
     * ```
     */
    protected static function fetchAll(string $sql, array $params = null, array $columns = null): array|object|null
    {
        self::init();
        if ($columns) {
            $sql = "SELECT " . implode(', ', $columns) . " FROM (" . $sql . ") as temp";
        }
        $results = self::$database->query($sql, $params)->findAll();
        return $results ?: null;
    }


    public static function query($sql, $params): object|bool
    {
        self::init();
        return self::$database->query($sql, $params)->find();
    }



    /**
     * Get a record from the database by its ID.
     *
     * @param int|string $id The ID of the record to retrieve.
     * @return object|null The retrieved record as an array, or null if not found.
     *
     * @example
     * ```php
     *  $user = User::findById(1);
     * if($user){
     *     echo 'User found';
     * }else{
     *     echo "user with id of 1 do not exist";
     * }
     * ```
     */
    public static function getById(int|string $id): ?object
    {
        self::init();
        $result = self::$database->query("SELECT " . implode(', ', static::$fields) . " FROM " . static::$table . " WHERE id = :id", ['id' => $id])->findOrFail();
        return $result ?: null;
    }


    /**
     * Get a record from the database by its slug.
     *
     * @param string $slug The slug of the record to retrieve.
     * @return object|null The retrieved record as an array, or null if not found.
     */
    public static function getBySlug(string $slug): ?object
    {
        $result = self::$database->query("SELECT " . implode(', ', static::$fields) . " FROM " . static::$table . " WHERE slug = :slug", ['slug' => $slug])->findOrFail();
        return $result ?: null;
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
        self::$database->query("DELETE FROM " . static::$table . " WHERE id = :id", ['id' => $id]);
    }

    /**
     * Save the model data to the database.
     *
     * @param array $data The data to save.
     * @return array|null The saved record as an array, or null if not saved.
     */
    public static function save(array $data): ?array
    {
        $id = $data['id'] ?? null;

        unset($data['id']);

        $table = static::$table;
        $updateData = $data;

        $updateColumns = implode(' = ?, ', array_keys($updateData)) . ' = ?';
        $params = array_values($updateData);

        if ($id) {
            $params[] = $id;
            $sql = "UPDATE $table SET $updateColumns WHERE id = ?";
        } else {
            $sql = "INSERT INTO $table (" . implode(', ', array_keys($data)) . ") VALUES (" . implode(', ', array_fill(0, count($data), '?')) . ")";
        }

        self::init();
        self::$database->query($sql, $params);

        if ($id) {
            $result = self::$database->query("SELECT * FROM $table WHERE id = ?", [$id]);
            return $result->findOrFail();
        }

        return null;
    }

    /**
     * Define a one-to-one relationship.
     *
     * @param string $modelName The related model name.
     * @param string|null $foreignKey The foreign key in the related table.
     * @param string $localKey The local key in this table.
     * @return object|null The related model instance, or null if not found.
     *
     * @example
     * ```php
     * // Example usage:
     * // Assuming the current model is 'User' and we want to retrieve the profile of the user.
     * $user = User::getById(1);
     * $profile = $user->hasOne('Profile', 'user_id');
     * if ($profile) {
     *     echo "User has a profile: " . $profile->name;
     * } else {
     *     echo "User does not have a profile.";
     * }
     * ```
     */
    public function hasOne(string $modelName, string $foreignKey = null, string $localKey = 'id'): ?object
    {
        $foreignKey = $foreignKey ?: strtolower(class_basename($this)) . '_id';
        return $modelName::where([$localKey => $this->{$foreignKey}])->first();
    }

    /**
     * Define a one-to-many relationship.
     *
     * @param string $modelName The related model name.
     * @param string|null $foreignKey The foreign key in the related table.
     * @param string $localKey The local key in this table.
     * @return array The related model instances.
     */
    public function hasMany(string $modelName, string $foreignKey = null, string $localKey = 'id'): array
    {
        $foreignKey = $foreignKey ?: strtolower(class_basename($this)) . '_id';
        return $modelName::where([$localKey => $this->{$foreignKey}])->get();
    }

    /**
     * Define a many-to-one relationship.
     *
     * @param string $modelName The related model name.
     * @param string|null $foreignKey The foreign key in this table.
     * @param string $localKey The local key in the related table.
     * @return object|null The related model instance, or null if not found.
     */
    public function belongsTo(string $modelName, string $foreignKey = null, string $localKey = 'id'): ?object
    {
        $foreignKey = $foreignKey ?: strtolower(class_basename($this)) . '_id';
        return $modelName::where([$localKey => $this->{$foreignKey}])->first();
    }

    /**
     * Define a many-to-many relationship.
     *
     * @param string $modelName The related model name.
     * @param string $table The pivot table name.
     * @param string|null $foreignKey The foreign key in the pivot table that references this table.
     * @param string|null $relatedKey The foreign key in the pivot table that references the related table.
     * @param string $localKey The local key in this table.
     * @return array The related model instances.
     */
    public function belongsToMany(string $modelName, string $table, string $foreignKey = null, string $relatedKey = null, string $localKey = 'id'): array
    {
        $foreignKey = $foreignKey ?: strtolower(class_basename($this)) . '_id';
        $relatedKey = $relatedKey ?: strtolower(class_basename($modelName)) . '_id';
        return $modelName::where([$localKey => $this->{$foreignKey}])->get();
    }

}
