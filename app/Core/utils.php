<?php

use App\Core\Response;
use App\Core\Router;
use JetBrains\PhpStorm\NoReturn;

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

    $jsonData = json_encode($data);
    echo
    <<<HTML
        <div id="dd-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 9999; overflow: auto;">
            <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background-color: #fff; border: 1px solid #ddd; padding: 10px; overflow-x: auto; font-size: 16px; line-height: 1.5; max-width: 80%; border-radius: 10px;">
                <pre id="dd-modal-content" style="max-height: 80vh; overflow: auto;"></pre>
                <button onclick="document.getElementById('dd-modal').style.display = 'none';">Close</button>
            </div>
        </div>
        <script>
            document.getElementById('dd-modal-content').innerText = JSON.stringify($jsonData, null, 2)
            document.getElementById('dd-modal').style.display = 'block';
        </script>

    HTML;
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

function findController(string $controllerName): ?string
{
    return base_path('/app/Controller/' . $controllerName . '.php');
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
 * @param callable $condition
 * @param int $status
 * @return true
 */
function authorize(callable $condition, int $status = Response::HTTP_FORBIDDEN): true
{
    if (!$condition) {
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
    exit($statusCode);

}


function url($path): string
{
    return "http://" . $_SERVER['HTTP_HOST'] . $path;
}

function asset($path): string
{
    return rtrim($_SERVER['DOCUMENT_ROOT'] . '/resources/' . ltrim($path, '/'), '/');
}

//function route($uri, ...$params): string
//{
//    $url = "http://" . $_SERVER['HTTP_HOST'] . $uri;
//    if (!empty($params)) {
//        $url .= '/' . implode('/', $params);
//    }
//    return $url;
//}


function route($uri, ...$params): string
{
    $url = "http://" . $_SERVER['HTTP_HOST'] . $uri;
    if (!empty($params)) {
        $url .= '/' . implode('/', $params);
    }

    // Get the attributes passed to the function
    $attributes = [];
    foreach (func_get_args() as $key => $value) {
        if ($key > 0 && is_array($value)) {
            $attributes = $value;
            break;
        }
    }

    // Remove the 'href' attribute if it exists
    unset($attributes['href']);

    // Build the HTML attributes string
    $attrs = '';
    foreach ($attributes as $key => $value) {
        $attrs .= " $key=\"$value\"";
    }

    return "<a href=\"$url\"$attrs>";
}

