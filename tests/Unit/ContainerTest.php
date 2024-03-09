<?php

use App\Core\Container;
use App\Core\Database;


test('it can resolve something out of the container', function () {
    // arrange
    $container = new Container();

    $container->bind('foo', fn() => 'bar');

    // act
    $result =$container->resolve('foo');


    // assert
    expect($result)->toEqual('bar');

});

test('it can resolve database out the container', function (){
    // arrange
    $container = new Container();

    $container->bind('database', fn() => Database::getInstance());

    // act
    $result = $container->resolve('database');

    // assert
    expect($result)->toBeInstanceOf(Database::class);
});
