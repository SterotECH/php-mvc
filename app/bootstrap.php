<?php

use App\App;
use App\Core\Container;
use App\Core\Database;

$container = new Container();

$container->bind(Database::class, function (){
    return Database::getInstance();
});

App::setContainer($container);
