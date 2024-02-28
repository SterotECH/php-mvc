<?php

namespace Utils;

use InvalidArgumentException;
use JetBrains\PhpStorm\NoReturn;

class Router
{
    protected array $routes = [];

    public function addRoute(string $method, string $uri, $controller): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $uri,
            'controller' => $controller,
        ];
    }

    public function get(string $uri, $controller): void
    {
        $this->addRoute('GET', $uri, $controller);
    }

    public function post(string $uri, $controller): void
    {
        $this->addRoute('POST', $uri, $controller);
    }

    public function put(string $uri, $controller): void
    {
        $this->addRoute('PUT', $uri, $controller);
    }

    public function patch(string $uri, $controller): void
    {
        $this->addRoute('PATCH', $uri, $controller);
    }

    public function delete(string $uri, $controller): void
    {
        $this->addRoute('DELETE', $uri, $controller);
    }

    public function resource(string $uri, $controller): void
    {
        $this->get($uri, $controller . '@index');
        $this->post($uri, $controller . '@store');
        $this->get("$uri/{id}", $controller . '@show');
        $this->put("$uri/{id}", $controller . '@update');
        $this->patch("$uri/{id}", $controller . '@update');
        $this->delete("$uri/{id}", $controller . '@destroy');
    }

    public function route(string $method, string $uri)
    {
        foreach ($this->routes as $route) {
            if ($route['method'] === strtoupper($method) && $route['uri'] === $uri) {
                return $this->callController($route['controller']);
            }
        }
        $this->abort();
    }

    protected function callController($controller)
    {
        if (is_callable($controller)) {
            return $controller();
        }

        if (is_string($controller)) {
            $parts = explode('@', $controller);
            $className = $parts[0];
            $methodName = $parts[1];

            require_once base_path("app/Controllers/$className.php");

            $obj = new $className();
            return $obj->$methodName();
        }

        throw new InvalidArgumentException('Invalid controller format');
    }

    #[NoReturn] protected function abort(int $statusCode = Response::HTTP_NOT_FOUND): void
    {
        http_response_code($statusCode);

        view('status/code', [
            'message' => Response::getStatusMessage($statusCode),
            'statusCode' => $statusCode,
        ]);
        die();
    }
}
