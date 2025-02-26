<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Nova Framework Core Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains all the configuration options for Nova Framework Core.
    | Framework can override these settings by creating a nova.php config file.
    |
    */

    'app' => [
        'name' => 'Nova Application',
        'env' => 'production',
        'debug' => false,
        'url' => 'http://localhost',
        'timezone' => 'UTC',
        'locale' => 'en',
    ],

    'security' => [
        'csrf' => [
            'enabled' => true,
            'token_length' => 32,
            'token_lifetime' => 3600
        ],
        'rate_limit' => [
            'enabled' => true,
            'default_max_attempts' => 60,
            'default_decay_minutes' => 1,
            'groups' => [
                'api' => ['max_attempts' => 100, 'decay_minutes' => 60],
                'auth' => ['max_attempts' => 5, 'decay_minutes' => 15]
            ]
        ],
        'headers' => [
            'x-frame-options' => 'SAMEORIGIN',
            'x-xss-protection' => '1; mode=block',
            'x-content-type-options' => 'nosniff'
        ]
    ],

    'database' => [
        'default' => 'mysql',
        'connections' => [
            'mysql' => [
                'driver' => 'mysql',
                'host' => 'localhost',
                'port' => 3306,
                'database' => 'nova',
                'username' => 'root',
                'password' => '',
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'maintanceMode' => false
            ]
        ]
    ],

    'cache' => [
        'default' => 'file',
        'stores' => [
            'file' => [
                'driver' => 'file',
                'path' => null
            ],
            'redis' => [
                'driver' => 'redis',
                'host' => 'localhost',
                'port' => 6379,
                'password' => null,
                'database' => 0
            ]
        ]
    ],

    'logging' => [
        'default' => 'file',
        'channels' => [
            'file' => [
                'driver' => 'file',
                'log_directory' => null,
                'log_levels' => ['error', 'warning', 'info', 'debug'],
                'log_filename_pattern' => '{level}-{date}.log',
                'max_log_size' => 5242880 // 5MB
            ]
        ]
    ],

    'mail' => [
        'default' => 'smtp',
        'mailers' => [
            'smtp' => [
                'host' => 'smtp.mailgun.org',
                'port' => 587,
                'encryption' => 'tls',
                'username' => null,
                'password' => null,
                'timeout' => 5
            ]
        ],
        'from' => [
            'address' => 'hello@example.com',
            'name' => 'Nova Framework'
        ]
    ],

    'router' => [
        'base_folder' => '',
        'main_method' => 'index',
        'paths' => [
            'controllers' => 'app/Controllers',
            'middlewares' => 'app/Middlewares'
        ],
        'namespaces' => [
            'controllers' => 'App\\Controllers',
            'middlewares' => 'App\\Middlewares'
        ],
        'debug' => false
    ],

    'view' => [
        'paths' => [
            'resources/views'
        ],
        'compiled' => 'storage/framework/views',
        'cache' => false
    ]
];
