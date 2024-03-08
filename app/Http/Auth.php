<?php

namespace App\Http;

class Auth
{
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
        unset($_SESSION);
        session_destroy();

        $params = session_get_cookie_params();

        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
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