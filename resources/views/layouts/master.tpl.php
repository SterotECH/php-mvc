<!DOCTYPE html>
<html lang="<?= substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)  ?>">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="description" content="<?= env('APP_DESCRIPTION', 'A simple PHP MVC application.') ?>" />
    <meta name="keywords" content="<?= env('APP_KEYWORDS', 'php, mvc, framework') ?>" />
    <meta name="author" content="<?= env('APP_AUTHOR', 'Samuel Agyei') ?>" />
    <meta name="robots" content="index, follow">
    <meta name="google-site-verification" content="<?= env('GOOGLE_SITE_VERIFICATION') ?>">
    <title><?= env('APP_NAME', 'PHP MVC'); ?></title>
    <link rel="canonical" href="<?php
        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $url = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        echo $url;
    ?>">
    <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    <link rel="stylesheet" href="<?= asset('/css/main.css') ?>" rel="stylesheet" />
    <?php require_once base_path('resources/views/layouts/scripts.php') ?>
</head>
<body class="text-gray-900 font-sans antialiased">
