<?php

namespace App\Core;

use App\Core\Interface\FormRequestInterface;
use App\Core\Interface\RequestInterface;

session_start();

class Request implements RequestInterface
{
    private ?object $server = null;
    private array $args = [];

    public ?FormRequestInterface $request = null;


    /**
     * @param array $args
     */
    public function __construct(array $args = [])
    {
        $this->server = new \stdClass();
        foreach ($_SERVER as $key => $value) {
            $this->server->{strtolower($key)} = $value;
        }

        $this->args = $args;
    }

    /**
     * @param FormRequestInterface $request
     * @return bool
     */
    public function validator(FormRequestInterface $request): mixed
    {
        return $request->validate($this);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    public function __get(string $name)
    {
        if (!in_array($name, (array)$this->server)) {
            throw new \Exception("property $name doesn't exists on collection instance request.");
        }
        return $this->server->{$name};
    }

    /**
     * @return object
     */
    public function server(): object
    {
        return $this->server;
    }

    /**
     * @return bool
     */
    public function ajax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';
    }

    /**
     * @return object
     */
    public function query(): object
    {
        return (object)$_GET ?? (object)$this->server->query_string;
    }

    /**
     * @return object
     */
    public function params(): object
    {
        return (object)$this->args;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function get(string $key): mixed
    {
        return array_key_exists($key, $_GET) ? $_GET[$key] : null;
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function post(string $key): mixed
    {
        return array_key_exists($key, $_POST) ? $_POST[$key] : null;
    }

    /**
     * @param string $key
     * @return string|null
     */
    public function input(string $key): string|null
    {
        if (array_key_exists($key, $_POST)) {
            return $_POST[$key] ?? null;
        }
        if (array_key_exists($key, $_GET)) {
            return $_GET[$key] ?? null;
        }
        return json_decode(file_get_contents("php://input"), false)?->{$key};
    }

    /**
     * @return mixed|object
     */
    public function all(): mixed
    {
        return match ($this->server()->request_method) {
            'POST' => (object)$_POST,
            'PUT', 'PATCH', 'DELETE' => json_decode(file_get_contents("php://input"), false),
            default => $this->query()
        };
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        $protocol = null;

        if (stripos($_SERVER['SERVER_PROTOCOL'], 'https') === 0) {
            $protocol = 'https';
        }
        if (stripos($_SERVER['SERVER_PROTOCOL'], 'http') === 0) {
            $protocol = 'http';
        }
        if (stripos($_SERVER['SERVER_PROTOCOL'], 'ftp') === 0) {
            $protocol = 'ftp';
        }

        return $protocol ?? $_SERVER['SERVER_PROTOCOL'];
    }

    /**
     * @return array|false|int|string|null
     */
    public function getHost(): false|array|int|string|null
    {
        return parse_url($_SERVER['HTTP_HOST'], PHP_URL_HOST);
    }

    /**
     * @return array|false|int|string|null
     */
    public function uri(): false|array|int|string|null
    {
        return parse_url($this->server()->request_uri, PHP_URL_PATH);
    }

    /**
     * @return string
     */
    public function ip(): string
    {
        return gethostbyname($this->getHost());
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function session(string $key = null): mixed
    {
        if (!isset($_SESSION[$key])) {
            return null;
        }
        return $_SESSION[$key];
    }

    /**
     * @param string|null $key
     * @return array|mixed
     */
    public function cookie(string $key = null): mixed
    {
        if (!isset($_COOKIE[$key])) {
            return null;
        }
        return $_COOKIE[$key];
    }


    /**
     * @return array|null
     */
    public function __debugInfo(): ?array
    {
        return (array)$this->server;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return json_encode($this->server);
    }
}
