<?php

namespace App\Models;


class Model
{
    use \App\Traits\Model;

    protected static ?string $whereClause = null;
    protected static array $whereParams = [];
    protected static ?string $joinClause = null;
    protected static array $joinParams = [];


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
        return $modelName::where([$localKey => $this->{$foreignKey}])->find();
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
