<?php

return [
    'name' => 'NovaPHP Basic Example',
    'env' => 'local',
    'debug' => true,
    'url' => 'http://localhost',
    'timezone' => 'Europe/Istanbul',
    'locale' => 'tr',

    'providers' => [
        \NovaCore\Providers\DatabaseServiceProvider::class,
        \NovaCore\Providers\RouteServiceProvider::class,
        \NovaCore\Providers\ViewServiceProvider::class,
        \App\Providers\AppServiceProvider::class,
    ],

    'middleware' => [
        'global' => [
            \NovaCore\Security\Middleware\SecurityMiddleware::class,
        ],
        'route' => [
            'auth' => \App\Middleware\AuthMiddleware::class,
            'guest' => \App\Middleware\GuestMiddleware::class,
        ],
    ],
];
