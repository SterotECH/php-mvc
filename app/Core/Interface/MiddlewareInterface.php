<?php

namespace App\Core\Interface;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, callable $next);
}