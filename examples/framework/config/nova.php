<?php

return [
    'app' => [
        'name' => 'My Nova Application',
        'env' => 'local',
        'debug' => true,
        'url' => 'http://myapp.test',
        'timezone' => 'Europe/Istanbul',
        'locale' => 'tr',
    ],

    'database' => [
        'connections' => [
            'mysql' => [
                'host' => 'db.myapp.test',
                'database' => 'myapp',
                'username' => 'myapp',
                'password' => 'secret'
            ]
        ]
    ],

    'mail' => [
        'mailers' => [
            'smtp' => [
                'host' => 'smtp.mailtrap.io',
                'username' => 'myusername',
                'password' => 'mypassword'
            ]
        ],
        'from' => [
            'address' => 'no-reply@myapp.test',
            'name' => 'My App'
        ]
    ],

    'logging' => [
        'channels' => [
            'file' => [
                'log_directory' => '/var/log/myapp',
                'log_levels' => ['error', 'warning', 'info']
            ]
        ]
    ]
];
