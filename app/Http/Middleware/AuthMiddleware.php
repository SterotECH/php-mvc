<?php

namespace App\Http\Middleware;

use App\Core\Authenticator;
use App\Core\Interface\MiddlewareInterface;
use App\Core\Interface\RequestInterface;

class AuthMiddleware implements MiddlewareInterface
{

    public function handle(RequestInterface $request, callable $next): void
    {
        if(!Authenticator::check()){
            header("Location: /auth/login");
            exit();
        }
        $next($request);
    }
}