<?php

use App\Core\Session;

?>
<!DOCTYPE html>
<html lang="<?= substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2); ?>" class="h-full bg-gray-100">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= env('APP_NAME', 'PHP MVC'); ?></title>
        <link href="<?= url('/css/styles.css') ?>" rel="stylesheet">
    </head>
    <body class="h-full transition-all">

