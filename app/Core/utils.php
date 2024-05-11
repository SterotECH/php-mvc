<?php

use App\Core\Exceptions\ConfigNotFoundException;
use App\Core\Response;
use App\Core\Session;
use JetBrains\PhpStorm\NoReturn;
use Random\RandomException;

/**
 * Require a config file from the config directory.
 *
 * @param string $filename The name of the config file (without the extension).
 * @return mixed The configuration array.
 * @throws Exception If the config file is not found.
 */
function config(string $filename): mixed
{
    $configDir = __DIR__ . '/../../config';
    $filePath = $configDir . '/' . $filename . '.php';

    if (file_exists($filePath)) {
        return require $filePath;
    } else {
        throw new ConfigNotFoundException($filename, $configDir);
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
                <button
                    onclick="document.getElementById('dd-modal').style.display = 'none';"
                    style="padding: 8px 16px; background-color: #dc3545; color: #fff; border: none; border-radius: 4px; cursor: pointer; margin-top: 10px;"
                >Close</button>
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


/**
 * @param string $path
 * @param array $data
 * @return void
 */
function view(string $path, array $data): void
{
    $viewFilePath = base_path('resources/views/' . $path . '.php');

    if (!file_exists($viewFilePath)) {
        abort(404);
    }

    extract($data);
    require $viewFilePath;
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

/**
 * @param string $uri
 * @return bool
 */
function route(string $uri): bool
{
    $currentUri = $_SERVER['REQUEST_URI'];
    return $currentUri === $uri;
}

function uriContains(string $value): bool
{
    $currentUri = $_SERVER['REQUEST_URI'];
    return str_contains($currentUri, $value);
}

/**
 * @param int $statusCode
 * @param string|null $description
 * @return void
 */
function abort(int $statusCode = Response::HTTP_NOT_FOUND, string|null|PDOException $description = null, string $view = 'status/code' ): void
{
    http_response_code($statusCode);

    view("$view", [
        'message' => Response::getStatusMessage($statusCode),
        'statusCode' => $statusCode,
        'description' => $description
    ]);
    exit($statusCode);
}


function asset($path): string
{
    $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    return $protocol . $_SERVER['HTTP_HOST'] . $path;
}


if (!function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param object|string $class
     * @return string
     */
    function class_basename(object|string $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;

        return basename(str_replace('\\', '/', $class));
    }
}

function redirect($url, int $status = Response::HTTP_MOVED_PERMANENTLY): void
{
    header("Location: $url");
    http_response_code($status);
    exit();
}

function old(string $key, mixed $default = '')
{
    return Session::get('old')[$key] ?? $default;
}


function displayError(array|string $errors): void
{
    if (is_array($errors)) {
        foreach ($errors as $error) {
            echo "<p class='mt-2 text-sm text-red-600'>$error</p>";
        }
    } else {
        echo "<p class='mt-2 text-sm text-red-600'>$errors</p>";
    }
}

function displaySuccess(string $message): string
{
    return "<p class='mt-2 text-sm text-green-600>$message</p>";
}


function csrf_field(): string
{
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    $csrfToken = $_SESSION['csrf_token'];


    return "<input type='hidden' name='csrf_token' value='$csrfToken'>";
}

function method_field(string $method): string
{
    return "<input type='hidden' name='_request_method' value='$method'>";
}

function formatColumnName(string $columnName): string
{
    return ucwords(str_replace('_', ' ', $columnName));
}


function resource($template){
    include base_path("resources/views/$template.tpl.php");
}

function url(string $path, array $params = []){
    $url = rtrim($_SERVER['REQUEST_URI'], '/') . '/' . ltrim($path, '/');
    if (!empty($params)) {
        $url .= '?' . http_build_query($params);
    }
    return $url;
}


function getHumanReadableDate($dateString)
{
    $date = new DateTime($dateString);

    $today = new DateTime();


    $diff = $date->diff($today);

    if ($diff->days == 0) {
        return 'today';
    } elseif ($diff->days == 1) {
        return 'tomorrow';
    } elseif ($diff->days == -1) {
        return 'yesterday';
    } elseif ($diff->days < 7 && $diff->days > -7) {
        return $date->format('l'); // Day of the week
    } else {
        return $date->format('Y-m-d'); // Date if more than a week
    }
}

function getHumanReadableTime($timeString)
{
    $time = new DateTime($timeString);

    $today = new DateTime();

    $diff = $time->diff($today);

    if ($diff->days == 0) {
        return $time->format('H:i');
    } else {
        return $time->format('Y-m-d H:i');
    }
}

function component(string $component, array $data = [])
{
    extract($data);

    require base_path("resources/views/components/$component.cmp.php");
}
