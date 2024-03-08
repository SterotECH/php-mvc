<?php

use App\Core\Router;
/**
 * @var string $message
 * @var int $statusCode
 * @var string $description
 */
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $statusCode ?> | <?= $message ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }

        .error-message {
            font-size: 24px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        pre {
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            padding: 10px;
            overflow-x: auto;
            font-size: 16px;
            line-height: 1.5;
            width: 80%;
            max-height: 200px;
            overflow-y: auto;
        }

        .route {
            justify-content: start;
            align-items: center;
            padding: 5px 0;
        }

        .method {
            width: 100px;
        }

        .method-delete {
            color: #dc3545;
        }

        .method-get {
            color: #007bff;
        }

        .method-post {
            color: #28a745;
        }

        .method-put {
            color: #ffc107;
        }

        .path {
            flex: 1;
            text-align: left;
        }

        .controller-method {
            font-size: 14px;
        }
    </style>
</head>

<body>
<div class="container">
    <div class="error-message">
        <h2><?= $statusCode ?></h2>
         &nbsp;|&nbsp;
        <h2><?= $message ?></h2>
    </div>
    <div class="bg-gray-100 rounded-lg shadow-md">
        <p class="text-sm text-gray-600"><?= $description ?></p>
    </div>
    <?php if (env('APP_ENV') === 'development' && $statusCode === 404): ?>
        <pre>
<?php
foreach (Router::$handlers as $handler) {
    $method = $handler['method'];
    $path = $handler['path'];
    $controller = '';
    $controllerMethod = '';
    if (is_array($handler['callback'])) {
        $controller = $handler['callback'][0];
        $controllerMethod = $handler['callback'][1];
    }
    echo '<div style="display: flex; align-items: center; ">';
    $dottedLineLength = max(70 - strlen($method) - strlen($path), 10);
    printf("<div class='route'>");
    printf("<span class='method method-%s'>%s</span>", strtolower($method), $method);
    printf("<span class='path method-%s'>%s %s</span>", strtolower($method), str_repeat("-", $dottedLineLength), $path);
    printf("</div>");

    if ($controller && $controllerMethod) {
        printf("<div class='controller-method method-%s''>&nbsp;{%s@%s}</div>",strtolower($method), $controller, $controllerMethod);
    }
    echo '</div>';
}
?>
            </pre>
    <?php endif; ?>
</div>
</body>

</html>
