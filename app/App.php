<?php

namespace App;

use App\Core\Container;
use Exception;

class App
{
    protected static Container $container;

    public static function setContainer(Container $container): void
    {
        static::$container = $container;
    }

    public static function container(): Container
    {
        return static::$container;
    }


    public static function resolve(string $key): void
    {
        static::container()->resolve($key);
    }

    public static function bind(string $key, callable $resolver): void
    {
        static::container()->bind($key, $resolver);
    }

}
