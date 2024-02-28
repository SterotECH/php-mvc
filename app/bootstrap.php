<?php

use Utils\App;
use Utils\Container;
use Utils\Database;

$container = new Container();

$container->bind(Database::class, function (){
    return new Database();
});

App::setContainer($container);