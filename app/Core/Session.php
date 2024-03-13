<?php

namespace App\Core;

class Session
{
    public static function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null)
    {
        return $_SESSION['_flash'][$key] ?? $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }


    public static function unflash(): void
    {
        unset($_SESSION['_flash']);
    }

    public static function flush(): void
    {
        $_SESSION = [];
    }

    public static function destroy(): void
    {
        self::flush();

        session_destroy();

        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);

        if (isset($_COOKIE['remember_token'])) {
            setcookie('remember_token', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
    }

    public static function token(): string
    {
        if (!self::has('_token')) {
            self::put('_token', bin2hex(random_bytes(32)));
        }

        return self::get('_token');
    }
}
