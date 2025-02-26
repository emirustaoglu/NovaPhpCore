<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Middleware Groups
    |--------------------------------------------------------------------------
    |
    | Middleware grupları birden fazla middleware'i bir arada kullanmayı sağlar.
    |
    */
    'groups' => [
        'web' => [
            \App\Middleware\StartSession::class,
            \App\Middleware\VerifyCsrfToken::class,
            \App\Middleware\ShareErrorsFromSession::class,
        ],
        'api' => [
            \App\Middleware\ThrottleRequests::class,
            \App\Middleware\ForceJsonResponse::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Middleware
    |--------------------------------------------------------------------------
    |
    | Tekil middleware'ler. Route'lara tek tek eklenebilir.
    |
    */
    'route' => [
        'auth' => \App\Middleware\Authenticate::class,
        'guest' => \App\Middleware\RedirectIfAuthenticated::class,
        'throttle' => \App\Middleware\ThrottleRequests::class,
        'verified' => \App\Middleware\EnsureEmailIsVerified::class,
    ],
];
