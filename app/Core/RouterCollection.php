<?php

namespace App\Core;

use App\Core\Interface\CollectionInterface;
use Exception;

class RouterCollection implements CollectionInterface
{

    /**
     * @param string $path
     * @return void
     * @throws Exception
     */
    public static function executeRoutesFrom(string $path): void
    {
        $routes = glob($path);
        if (is_array($routes)) {
            foreach ($routes as $route) {
                if (file_exists($route)) {
                    require $route;
                } else {
                    throw new Exception("$route file doesn't exists.");
                }
            }
            Router::executeRoutes();
        }
    }
}