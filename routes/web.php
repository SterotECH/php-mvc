<?php

use App\Controller\HomeController;
use App\Controller\UserController;
use App\Controller\NotesController;
use App\Core\RequestInterface;
use App\Core\Router;

Router::get('/', [HomeController::class , 'index']);
Router::resource('/users',UserController::class);

