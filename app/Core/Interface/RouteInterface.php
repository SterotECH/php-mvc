<?php

namespace App\Core\Interface;

interface RouteInterface
{
    public function middleware(array $middlewares):RouteInterface;

    public function where(string $pattern):RouteInterface;
}