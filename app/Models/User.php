<?php

namespace App\Models;

use DateTime;

class User extends Model
{
    protected static ?string $table = 'users';

    protected static array $fields = [
        'id',
        'username',
        'email',
        'first_name',
        'last_name',
        'phone_number',
        'created_at',
        'password'
    ];

    public int $id;
    public string $username;
    public string $email;
    public string $first_name;
    public string $last_name;
    public string $other_name;
    public string $phone_number;
    public string $password;
    public DateTime $updated_at;
    public DateTime $created_at;

    public function notes(): array
    {
        return $this->hasMany(Note::class,'user_id');
    }

}
