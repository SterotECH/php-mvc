<?php

namespace App\Models;


class Note extends Model{
    protected static ?string $table = 'notes';

    protected static array $fields = [
        'id',
        'body',
        'user_id',
    ];
}