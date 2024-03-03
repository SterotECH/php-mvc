<?php

use App\Core\Router;

class Route
{
    protected static Router $router;

    public static function setRouter($router): void
    {
        static::$router = $router;
    }

    public static function __callStatic($method, $args)
    {
        if (!static::$router) {
            throw new RuntimeException('Router instance not set. Call setRouter() first.');
        }

        return static::$router->$method(...$args);
    }
}
