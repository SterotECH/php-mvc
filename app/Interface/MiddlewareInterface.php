<?php

namespace App\Interface;

interface MiddlewareInterface
{
    public function handle(RequestInterface $request, callable $next);
}