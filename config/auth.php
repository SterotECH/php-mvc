<?php

$params = session_get_cookie_params();
return [
    'driver' => 'database',

    'database' => [
        'table' => 'users',
        'username' => 'email',
        'password' => 'password',
    ],
    'session' => [
        'name' => 'user',
        'lifetime' => 3600,
        'path' => $params['path'],
        'domain' =>  $params['domain'],
        'secure' =>  $params['secure'],
        'httponly' => $params['httponly'],
    ],

    'hash' => [
        'algorithm' => PASSWORD_DEFAULT,
        'options' => [

        ],
    ],

    'paths' => [
        'login' => '/auth/login',
        'logout' => '/auth/logout',
        'home' => '/',
    ],
];
