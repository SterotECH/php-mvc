<?php
declare(strict_types=1);

use App\Core\Router;
use App\Core\Session;
use Dotenv\Dotenv;

const BASE_PATH = __DIR__ . '/../';
require_once BASE_PATH . 'vendor/autoload.php';

require_once BASE_PATH . 'app/Core/utils.php';

$dotenv = Dotenv::createImmutable(BASE_PATH);
$dotenv->load();


require base_path('routes/web.php');

try {
    Router::executeRoutes();
}catch (Exception $e) {
    error_log($e->getMessage());
}

Session::unflash();
