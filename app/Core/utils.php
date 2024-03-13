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


function dd(...$data): void
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
function abort(int $statusCode = Response::HTTP_NOT_FOUND, string|null $description = null): void
{
    http_response_code($statusCode);

    view('status/code', [
        'message' => Response::getStatusMessage($statusCode),
        'statusCode' => $statusCode,
        'description' => $description
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

function redirect($url): void
{
    header("Location: $url");
    exit();
}

function old(string $key, mixed $default = '')
{
    return Session::get('old')[$key] ?? $default;
}

function showToast($message, $color, $type): void
{
    if (is_array($message)) {
        $errorMessages = '';
        foreach ($message as $error) {
            $errorMessages .= <<< HTML
                                <div id="toast" class="flex items-center p-4 mb-4 text-sm text-$color-800 rounded-lg bg-$color-50 dark:bg-gray-800 dark:text-$color-400 z-50 transition-all" role="alert">
                                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                    </svg>
                                    <span class="sr-only">$type</span>
                                <div>
                               $error
                            </div>
                            </div>
                            HTML;
        }
                echo $errorMessages;
    } else {
        echo <<< HTML
                                <div id="toast" class="flex items-center p-4 mb-4 text-sm text-$color-800 rounded-lg bg-$color-50 dark:bg-gray-800 dark:text-$color-400 z-50 transition-all" role="alert">
                                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                                    </svg>
                                    <span class="sr-only">$type</span>
                                <div>
                               $message
                            </div>
                            </div>
                            HTML;
    }
    echo "<script >
              const toast = document . getElementById('toast');
              toast . classList . remove('hidden');
              setTimeout(() => {
            toast . classList . add('hidden');
            }, 3000);
        </script > ";
}

function errorToast($message): void
{
    showToast($message, 'red', 'error');
}

function successToast($message): void
{
    showToast($message, 'green', 'success');
}

function warningToast($message): void
{
    showToast($message, 'amber', 'danger');
}

function infoToast($message): void
{
    showToast($message, 'blue', 'info');
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

/**
 * @throws RandomException
 */
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

