<?php

namespace App\Http\Middleware;

use App\Core\Authenticator;
use App\Core\Interface\MiddlewareInterface;
use App\Core\Interface\RequestInterface;

class GustMiddleware implements MiddlewareInterface
{

    public function handle(RequestInterface $request, callable $next): void
    {
        if(Authenticator::check()){
            header("Location: /");
            exit();
        }
        $next($request);
    }
}