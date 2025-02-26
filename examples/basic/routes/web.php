<?php

use NovaCore\Router\Router;

/** @var Router $router */

// Ana sayfa
$router->get('/', 'HomeController@index')->name('home');

// Upload örneği
$router->group(['prefix' => 'upload'], function(Router $router) {
    $router->get('/', 'UploadController@showForm')->name('upload.form');
    $router->post('/', 'UploadController@handle')->name('upload.handle');
});

// Database örneği
$router->group(['prefix' => 'users'], function(Router $router) {
    $router->get('/', 'UserController@index')->name('users.index');
    $router->get('/{id}', 'UserController@show')->name('users.show');
    $router->post('/', 'UserController@store')->name('users.store');
    $router->put('/{id}', 'UserController@update')->name('users.update');
    $router->delete('/{id}', 'UserController@delete')->name('users.delete');
});
