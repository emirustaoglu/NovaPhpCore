<?php

return [
    'default' => 'mysql',

    'connections' => [
        'mysql' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'database' => 'novaphp_basic',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'strict' => true,
            'engine' => null,
        ],
    ],

    'redis' => [
        'client' => 'predis',
        'default' => [
            'host' => 'localhost',
            'password' => null,
            'port' => 6379,
            'database' => 0,
        ],
        'cache' => [
            'host' => 'localhost',
            'password' => null,
            'port' => 6379,
            'database' => 1,
        ],
    ],
];
