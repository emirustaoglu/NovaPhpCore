<?php

use NovaCore\Router\Router;

/** @var Router $router */

// Ana sayfa
$router->get('/', 'HomeController@index')->name('home');

// Auth routes
$router->group(['prefix' => 'auth', 'namespace' => 'Auth'], function(Router $router) {
    $router->get('login', 'LoginController@showLoginForm')->name('login');
    $router->post('login', 'LoginController@login');
    $router->post('logout', 'LoginController@logout')->name('logout');
    $router->get('register', 'RegisterController@showRegistrationForm')->name('register');
    $router->post('register', 'RegisterController@register');
});

// Admin panel routes
$router->group(['prefix' => 'admin', 'middleware' => ['auth', 'admin']], function(Router $router) {
    $router->get('/', 'Admin\DashboardController@index')->name('admin.dashboard');
    
    // Users
    $router->group(['prefix' => 'users'], function(Router $router) {
        $router->get('/', 'Admin\UserController@index')->name('admin.users.index');
        $router->get('create', 'Admin\UserController@create')->name('admin.users.create');
        $router->post('/', 'Admin\UserController@store')->name('admin.users.store');
        $router->get('{id}', 'Admin\UserController@show')->name('admin.users.show');
        $router->get('{id}/edit', 'Admin\UserController@edit')->name('admin.users.edit');
        $router->put('{id}', 'Admin\UserController@update')->name('admin.users.update');
        $router->delete('{id}', 'Admin\UserController@destroy')->name('admin.users.destroy');
    });
});

// API routes
$router->group(['prefix' => 'api', 'middleware' => 'api'], function(Router $router) {
    $router->get('users', 'Api\UserController@index');
    $router->get('users/{id}', 'Api\UserController@show');
    $router->post('users', 'Api\UserController@store');
    $router->put('users/{id}', 'Api\UserController@update');
    $router->delete('users/{id}', 'Api\UserController@destroy');
});
