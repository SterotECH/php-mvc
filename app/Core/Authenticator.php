<?php

namespace App\Core;

use App\Http\Auth;
use App\Models\User;

class Authenticator
{
    public static function attempt(string $email, string $password): bool
    {
        $user = User::where(['email' => $email]);

        if ($user) {
            if (password_verify($password, $user[0]->password)) {
                self::login($user);
                return true;
            }
        }
        return false;
    }

    public static function login($user): void
    {
        $_SESSION['user'] = [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'email' => $user->email
        ];

        session_regenerate_id(delete_old_session: true);
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }

    public static function check(): bool
    {
        return isset($_SESSION['user']);
    }
}