<?php

namespace App\Models;

use App\Core\Database;

class Model
{
    protected static Database $database;

    public static function init(): void
    {
        self::$database = Database::getInstance();
    }

    public static function all(): array
    {
        return self::fetchAll("SELECT * FROM " . static::$table);
    }

    public static function create(array $data): bool
    {
        self::init();
        $columns = implode(', ', array_keys($data));
        $values = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO " . static::$table . " ($columns) VALUES ($values)";
        $params = array_values($data);
        return self::$database->query($sql, $params)->rowCount() > 0;
    }

    public static function where(array $conditions): array
    {
        $sql = "SELECT * FROM " . static::$table . " WHERE ";
        $sql .= implode(' AND ', array_map(fn($key) => "$key = ?", array_keys($conditions)));
        $params = array_values($conditions);
        return self::fetchAll($sql, $params);
    }

    public static function get(array $columns): array
    {
        return self::fetchAll("SELECT " . implode(', ', $columns) . " FROM " . static::$table);
    }

    public static function distinct(string $column): array
    {
        return self::fetchAll("SELECT DISTINCT $column FROM " . static::$table);
    }

    public static function paginate(int $page, int $perPage = 10): array
    {
        return self::fetchAll("SELECT * FROM " . static::$table . " LIMIT :perPage OFFSET :offset", [
            'perPage' => $perPage,
            'offset' => ($page - 1) * $perPage
        ]);
    }

    protected static function fetchAll(string $sql, array $params = null): array
    {
        self::init();
        return self::$database->query($sql)->findAll();
    }

    public static function getById(int|string $id): ?array
    {
        self::init();
        $result = self::$database->query("SELECT * FROM ". static::$table ." WHERE id = :id", ['id'=>$id])->findOrFail();
        return $result ?: null;
    }

    public static function getBySlug(string $slug): ?array
    {
        $result = self::$database->query("SELECT * FROM ".static::$table." WHERE slug = :slug", ['slug'=>$slug])->findOrFail();
        return $result ?: null;
    }

    public static function delete(string|int $id)
    {
        dd($id);
    }

    public static function save(array $data)
    {

    }
}
