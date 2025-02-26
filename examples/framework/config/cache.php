<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | Varsayılan cache sürücüsü.
    | Desteklenen sürücüler: "file", "redis", "memcached", "array"
    |
    */
    'default' => 'redis',

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Cache sürücülerinin ayarları. Her sürücü için ayrı ayarlar yapılabilir.
    |
    | File Driver:
    | - Dosya tabanlı cache sistemi
    | - Basit ve kurulum gerektirmez
    | - Küçük/orta ölçekli uygulamalar için uygundur
    |
    | Redis Driver:
    | - Yüksek performanslı key-value store
    | - Dağıtık cache için uygundur
    | - Büyük ölçekli uygulamalar için önerilir
    |
    */
    'stores' => [
        'file' => [
            // Cache sürücüsü
            'driver' => 'file',
            
            // Cache dosyalarının saklanacağı dizin
            'path' => storage_path('framework/cache'),
            
            // Dosya izinleri
            'permission' => 0777,
            
            // Cache dosyası uzantısı
            'extension' => '.cache'
        ],

        'redis' => [
            // Cache sürücüsü
            'driver' => 'redis',
            
            // Redis sunucu adresi
            'host' => 'localhost',
            
            // Redis port numarası
            'port' => 6379,
            
            // Redis şifresi (varsa)
            'password' => null,
            
            // Redis veritabanı numarası
            'database' => 0,
            
            // Bağlantı zaman aşımı (saniye)
            'timeout' => 0.0,
            
            // Önbellek anahtarı prefix'i
            'prefix' => 'nova_cache:'
        ],

        'memcached' => [
            'driver' => 'memcached',
            'servers' => [
                [
                    'host' => '127.0.0.1',
                    'port' => 11211,
                    'weight' => 100
                ],
            ],
        ],

        'array' => [
            'driver' => 'array',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | Önbellek anahtarlarına eklenecek prefix.
    | Aynı sunucuda çalışan farklı uygulamaların cache'lerini ayırmak için kullanılır.
    |
    */
    'prefix' => 'nova',

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (Time To Live)
    |--------------------------------------------------------------------------
    |
    | Varsayılan cache süresi (saniye)
    | null = sonsuza kadar
    |
    */
    'ttl' => 3600,
];
