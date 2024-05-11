<?php

namespace App\Core;

use App\Models\User;
use Exception;
use Random\RandomException;

class Authenticator
{


    /**
     * @throws RandomException
     */
    public static function attempt(string $email, string $password): bool
    {
        $config = require base_path('config/auth.php');
        $user = User::where([$config['database']['username'] => $email])
            ->first([
                'password',
                'id',
                'first_name',
                'last_name',
                'role',
                'is_active',
                'email',
                'phone_number',
                'profile_picture',
                'is_superuser'
            ]);
        if ($user && password_verify($password, $user->{$config['database']['password']}) && $user->is_active) {
                self::login($user);
                return true;
            }
        return false;
    }

    /**
     * @throws RandomException
     */
    public static function login($user, bool $remember = false): void
    {
        $config = require base_path('config/auth.php');

        $_SESSION[$config['session']['name']] = [
            'id' => $user->id,
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'phone_number' => $user->phone_number,
            'email' => $user->email,
            'profile_picture' => $user->profile_picture,
            'role' =>$user->role,
            'is_superuser' => $user->is_superuser,
        ];

        session_regenerate_id(delete_old_session: true);

        if ($remember) {
            $token = bin2hex(random_bytes(32));
            $user->remember_token = $token;
            $user->save();

            setcookie(
                'remember_token',
                $token,
                time() + (30 * 24 * 60 * 60),
                $config['session']['path'],
                $config['session']['domain'],
                $config['session']['secure'],
                $config['session']['httponly']
            );
        }
    }

    public static function logout(): void
    {
        Session::destroy();
    }

    public static function user(): object|null
    {
        $config = require base_path('config/auth.php');
        return (object)$_SESSION[$config['session']['name']] ?? null;
    }

    public static function check(): bool
    {
        $config = require base_path('config/auth.php');
        return isset($_SESSION[$config['session']['name']]);
    }

    public static function register(array $data): array|object
    {
        $config = require base_path('config/auth.php');
        $user = new User();
        $user->username = $data['username'];
        $user->first_name = $data['first_name'];
        $user->last_name = $data['last_name'];
        $user->other_name = $data['other_name'] ?? null;
        $user->phone_number = $data['phone_number'];
        $user->email = $data['email'];
        $user->address = $data['address'];
        $user->gender = $data['gender'];
        $user->date_of_birth = $data['date_of_birth'];
        $user->profile_picture = $data['profile_picture'];
        $user->role = $data['role'] ?? User::CUSTOMER;
        $user->password = password_hash($data['password'], $config['hash']['algorithm'], $config['hash']['options']);

        return $user->save();
    }
}
