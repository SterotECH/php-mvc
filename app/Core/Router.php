<?php

namespace App\Core;

use App\Core\Interface\MiddlewareInterface;
use Exception;

class Router
{
    private static ?Router $instance = null;
    public static ?array $handlers;
    private const GET_METHOD = 'GET';
    private const POST_METHOD = 'POST';
    private const PUT_METHOD = 'PUT';
    private const PATCH_METHOD = 'PATCH';
    private const DELETE_METHOD = 'DELETE';
    public ?string $method = 'GET';
    private array $args = [];
    public ?string $path, $uri = '/';

    private array $validMethods = [
        'GET', 'POST', 'PUT', 'PATCH', 'DELETE'
    ];

    private ?string $serverMode;
    private ?string $prefix = null;
    private ?array $routes = [];
    private array $middlewares = [];
    private ?string $host;


    /**
     *
     * @throws Exception
     */
    private function __construct()
    {
        $this->serverMode = php_sapi_name();
        $this->uri = $this->serverMode === 'cli-server' ? parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : null;
        $this->host = env('HOSTNAME','localhost');
        self::$handlers = $this->routes;
    }

    /**
     * create a singleton instance pattern to instantiate only one object from this class
     * @return static|null
     */
    private static function getInstance(): ?static
    {
        if (static::$instance === null || is_null(static::$instance)) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return Router
     */
    public static function get(string $path, callable|array $callback): Router
    {
        return static::getInstance()->addRoute(self::GET_METHOD, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return Router
     */
    public static function post(string $path, callable|array $callback): Router
    {
        return static::getInstance()->addRoute(self::POST_METHOD, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return Router
     */
    public static function put(string $path, callable|array $callback): Router
    {
        return static::getInstance()->addRoute(self::PUT_METHOD, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return Router
     */
    public static function patch(string $path, callable|array $callback): Router
    {
        return static::getInstance()->addRoute(self::PATCH_METHOD, $path, $callback);
    }

    /**
     * @param string $path
     * @param callable|array $callback
     * @return Router
     */
    public static function delete(string $path, callable|array $callback): Router
    {
        return static::getInstance()->addRoute(self::DELETE_METHOD, $path, $callback);
    }

    public static function resource(string $name, string $controller, array $middleware = []): Router
    {

        static::getInstance()->get("$name", [$controller, 'index'])->middleware($middleware);
        static::getInstance()->get("$name/create", [$controller, 'create'])->middleware($middleware);
        static::getInstance()->post("$name", [$controller, 'store'])->middleware($middleware);
        static::getInstance()->get("$name/{id}", [$controller, 'show'])->middleware($middleware);
        static::getInstance()->get("$name/{id}/edit", [$controller, 'edit'])->middleware($middleware);
        static::getInstance()->put("$name/{id}", [$controller, 'update'])->middleware($middleware);
        static::getInstance()->patch("$name/{id}", [$controller, 'update'])->middleware($middleware);
        static::getInstance()->delete("$name/{id}", [$controller, 'destroy'])->middleware($middleware);

        return static::getInstance();
    }



    /**
     * @param array $methods
     * @param string $path
     * @param callable|array $callback
     * @return Router
     * @throws Exception
     */
    public static function match(array $methods, string $path, callable|array $callback): Router
    {
        foreach ($methods as $method) {
            static::getInstance()->addRoute($method, $path, $callback);
        }

        return static::getInstance();
    }


    /**
     * @return void
     * @throws Exception
     */
    public static function executeRoutes(): void
    {
        static::getInstance()->serve();
    }


    /**
     * @param array $middlewares
     * @return Router
     */
    public function middleware(array $middlewares): Router
    {
        foreach ($this->routes as $index => $route) {
            if ($route['domain'] . $route['path'] === $this->host . $this->path) {
                $this->routes[$index]['middlewares'] = [...$middlewares, ...$this->middlewares];
            }
        }

        return $this;
    }

    /**
     * @param string $pattern
     * @return Router
     */
    public function where(string $pattern): Router
    {
        preg_match($pattern, $this->uri, $matches);

        foreach ($this->routes as $index => $route) {
            if ($route['domain'] . $route['path'] === $this->host . $this->path) {
                $this->routes[$index]['valid'] = count($matches) > 0;
            }
        }

        return $this;
    }


    /**
     * @param array $attributes
     * @param callable $callback
     * @return void
     */
    public static function group(array $attributes, callable $callback): void
    {
        if (array_key_exists('prefix', $attributes)) {
            static::getInstance()->prefix = $attributes['prefix'];
        }
        if (array_key_exists('middleware', $attributes)) {
            static::getInstance()->middlewares = is_array($attributes['middleware']) && count($attributes['middleware']) ? [...$attributes['middleware']] : $attributes['middleware'];
        }
        if (array_key_exists('domain', $attributes)) {
            static::getInstance()->host = $attributes['domain'];
        }

        call_user_func($callback);

        static::getInstance()->prefix = null;
        static::getInstance()->middlewares = [];
        static::getInstance()->host = env('HOSTNAME', ';localhost');
    }


    /**
     * @param string $method
     * @param mixed $path
     * @param callable|array $callback
     * @return Router
     */
    private function addRoute(string $method, mixed $path, callable|array $callback): Router
    {
        if (!in_array($method, $this->validMethods)) {
            http_response_code(405);
            abort(Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $this->path = $this->prefix !== null ? $this->prefix . $path : $path;

        foreach ($this->routes as $index => $route) {
            if ($route['domain'] . $route['path'] === $this->host . $this->path && $route['method'] === $method) {
                throw new Exception("route $path added before.");
            }
        }

        $this->routes[] = [
            'method' => $method,
            'path' => $this->path,
            'callback' => $callback,
            'middlewares' => [...$this->middlewares],
            'valid' => true,
            'domain' => $this->host
        ];

        return $this;
    }

    /**
     * @return void
     * @throws Exception
     */
    private function serve(): void
    {
        $method = $_POST['_request_method'] ?? $_SERVER['REQUEST_METHOD'];
        self::$handlers = $this->routes;

    foreach ($this->routes as $index => $route) {
            if ($this->handleDynamicRouteParamsAndPath($route['path'], $this->uri) &&
                $route['method'] === $method &&
                $route['valid']
            ) {
                if ($this->checkDomain($route['domain'])) {
                    $this->handleRoute($route['callback'], $route['middlewares']);
                    return;
                }else{
                    abort(Response::HTTP_PRECONDITION_REQUIRED);
                }
            }
        }

        abort();
    }


    /**
     * @param array|callable $callback
     * @param array $middlewares
     * @return void
     * @throws Exception
     */
    private function handleRoute(array|callable $callback, array $middlewares): void
    {
        $request = new Request($this->args);

        $next = function ($request) use ($callback) {
            $this->handleCallback($callback, $request);
        };

        $middlewares = array_reverse($middlewares);

        foreach ($middlewares as $middleware) {
            $instance = new $middleware();
            if ($instance instanceof MiddlewareInterface) {
                $nextMiddleware = $next;
                $next = function ($request) use ($instance, $nextMiddleware) {
                    $instance->handle($request, $nextMiddleware);
                };
            } else {
                throw new Exception("$middleware must be type of MiddlewareInterface interface.");
            }
        }

        $next($request);
    }


    /**
     * @param callable|array $callback
     * @param Request $request
     * @return void
     */
    private function handleCallback(callable|array $callback, Request $request): void
    {
        is_array($callback) ?
            call_user_func_array([new $callback[0], $callback[1]], [$request]) :
            call_user_func($callback, $request);
    }

    /**
     * @param string $domain
     * @return bool
     */
    private function checkDomain(string $domain): bool
    {
        return $domain === parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
    }

    /**
     * @param string $route
     * @param string $uri
     * @return bool
     */
    private function handleDynamicRouteParamsAndPath(string $route, string $uri): bool
    {
        $pattern = "/{(.*?)}/";
        preg_match_all($pattern, $route, $matches);

        $uriArray = explode('/', $this->uri);
        $pathArray = explode('/', $route);
        $uriDiff = array_diff($uriArray, $pathArray);
        $path = "";
        if (count($matches[1]) === count($uriDiff)) {
            $this->args = [...array_combine($matches[1], $uriDiff)];
            $path = sprintf(preg_replace("$pattern", "%s", $route), ...array_values($this->args));
        }

        return $path === $uri;
    }

    public static function previousUrl(): string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public function __destruct()
    {
        $this->args = [];
    }
}
