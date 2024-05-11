<?php

use App\Core\Router;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\AuthMiddleware;
use App\Http\Middleware\GustMiddleware;

Router::get('/', [HomeController::class , 'index'])->middleware([AuthMiddleware::class]);
Router::get('/auth/login', [HomeController::class, 'render_login'])->middleware([GustMiddleware::class]);
Router::post('/auth/login', [HomeController::class, 'login']);
Router::get('/auth/register', [HomeController::class, 'render_register'])->middleware([GustMiddleware::class]);
Router::post('/auth/register', [HomeController::class, 'register'])->middleware([GustMiddleware::class]);
Router::delete('/auth/logout', [HomeController::class, 'logout']);
Router::get('/auth/forgot-password', [HomeController::class, 'render_forgot_password']);
Router::resource('/users',UserController::class);
