<?php

namespace App\Core\Interface;

use Closure;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, Closure $next);
}
