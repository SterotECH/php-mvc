<?php

namespace App\Models;


class Note extends Model{
    protected static ?string $table = 'notes';

    protected static array $fields = [
        'id',
        'content',
        'created_at',
        'updated_at',
        'user_id',
    ];
}
