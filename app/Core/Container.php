<?php

namespace App\Core;

use Exception;

class Container
{

    protected array $bindings = [];

    /**
     * @param string $key
     * @param callable $resolver
     * @return void
     */
    public function bind(string $key, callable $resolver): void
    {
        $this->bindings[$key] = $resolver;
    }



    public function resolve(string $key): mixed
    {
        if (!array_key_exists($key, $this->bindings)){
           echo "No binding found for {$key}";
        }
        $resolver = $this->bindings[$key];

        return call_user_func($resolver);
    }

}