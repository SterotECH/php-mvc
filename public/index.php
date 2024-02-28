<?php

use Dotenv\Dotenv;
use Utils\Router;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'app/Utils/utils.php';
require base_path('vendor/autoload.php');

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';
    $classFile = base_path("app/$classPath");

    if (file_exists($classFile)) {
        require $classFile;
    }
});

$router = new Router();

$routes = require base_path('routes/web.php');

$uri = parse_url($_SERVER['REQUEST_URI'])['path'];
$method = $_POST['_request_method'] ?? $_SERVER['REQUEST_URI'];

$router->route($uri, $method);
