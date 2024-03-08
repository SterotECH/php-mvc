<?php

namespace App\Http\Middleware;

use App\Core\Interface\MiddlewareInterface;
use App\Core\Interface\RequestInterface;

class GustMiddleware implements MiddlewareInterface
{

    public function handle(RequestInterface $request, callable $next): void
    {
        if(isset($_SESSION['user'])){
            header("Location: /");
            exit();
        }
        $next($request);
    }
}