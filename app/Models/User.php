<?php

namespace App\Models;

class User extends Model {
    protected static ?string $table = 'users';
    protected static array $hiddenColumns = [
        'password',
        'username',
        'created_at',
        'updated_at'
    ];

}