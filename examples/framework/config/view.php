<?php

return [
    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | View dosyalarının bulunduğu dizinler.
    | Birden fazla dizin tanımlanabilir.
    |
    */
    'paths' => [
        resource_path('views'),
    ],

    /*
    |--------------------------------------------------------------------------
    | View Cache Path
    |--------------------------------------------------------------------------
    |
    | Blade template cache dosyalarının saklanacağı dizin.
    |
    */
    'cache' => storage_path('app/cache'),

    /*
    |--------------------------------------------------------------------------
    | Blade Cache TTL
    |--------------------------------------------------------------------------
    |
    | Cache dosyalarının geçerlilik süresi (saniye)
    | null: süresiz
    | 0: cache devre dışı
    | >0: belirtilen süre kadar
    |
    */
    'ttl' => null,
];
