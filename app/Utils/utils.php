<?php

use JetBrains\PhpStorm\NoReturn;
use Utils\Response;

/**
 * @throws Exception
 */
function config($filename)
{
    $configDir = __DIR__ . '/../../config';

    $filePath = $configDir . '/' . $filename . '.php';

    if (file_exists($filePath)) {
        return require_once $filePath;
    } else {
        throw new Exception("Config file '$filename' not found in '$configDir'");
    }

}

/**
 * @param $key
 * @param $default
 * @return mixed|null
 */
function env($key, $default = null): mixed
{
    if (array_key_exists($key, $_ENV)) {
        return $_ENV[$key];
    }

    return $default;
}


function dd($data): void
{
    if (env('APP_ENV') !== 'development') {
        return;
    }

    echo '<pre style="background-color: #f5f5f5; border: 1px solid #ddd; padding: 10px; overflow-x: auto; font-size: 16px; line-height: 1.5;">';
    echo htmlspecialchars(var_export($data, true), ENT_QUOTES);
    echo '</pre>';
    die();
}

/**
 * @param $path
 * @return string
 */
function base_path($path): string
{
    return BASE_PATH . $path;
}

/**
 * @param string $path
 * @param array $data
 * @return void
 */
function view(string $path, array $data): void
{
    extract($data);
    require base_path('resources/views/' . $path . '.php');
}

/**
 * @param $condition
 * @param int $status
 * @return true
 */
function authorize($condition, int $status = \Utils\Response::HTTP_FORBIDDEN): true
{
    if (!$condition){
        abort($status);
    }
    return true;
}

#[NoReturn] function abort(int $statusCode = Response::HTTP_NOT_FOUND): void
{
    http_response_code($statusCode);

    view('status/code', [
        'message' => Response::getStatusMessage($statusCode),
        'statusCode' => $statusCode,
    ]);
    die();

}