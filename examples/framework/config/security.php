<?php

return [
    'csrf' => [
        'enabled' => true,
        'token_length' => 64, // Core'dan farklı bir değer
    ],
    'rate_limit' => [
        'groups' => [
            'api' => [
                'max_attempts' => 200, // Core'dan farklı bir değer
                'decay_minutes' => 60
            ],
            'web' => [ // Yeni bir grup
                'max_attempts' => 300,
                'decay_minutes' => 30
            ]
        ]
    ]
];
