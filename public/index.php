<?php
declare(strict_types=1);

use App\Core\Router;
use Dotenv\Dotenv;

const BASE_PATH = __DIR__ . '/../';

require_once BASE_PATH . 'app/Core/utils.php';
require base_path('vendor/autoload.php');


$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();

spl_autoload_register(function ($class) {
    $classPath = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $classFile = base_path("app/$classPath.php");

    if (file_exists($classFile)) {
        require $classFile;
    }
});

require base_path('routes/web.php');

Router::executeRoutes();

