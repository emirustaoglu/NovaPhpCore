<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Name
    |--------------------------------------------------------------------------
    |
    | Uygulama adı. Bu değer uygulama genelinde kullanılabilir.
    | Örneğin: mail gönderimlerinde, sayfa başlıklarında vb.
    |
    */
    'name' => 'My Nova Application',

    /*
    |--------------------------------------------------------------------------
    | Application Environment
    |--------------------------------------------------------------------------
    |
    | Uygulamanın çalıştığı ortam.
    | Değerler: 'local', 'staging', 'production'
    |
    */
    'env' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Application Debug Mode
    |--------------------------------------------------------------------------
    |
    | Debug modu açıkken detaylı hata mesajları gösterilir.
    | Production ortamında kapalı tutulmalıdır.
    |
    */
    'debug' => true,

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    |
    | Uygulamanın çalıştığı URL. Mail gönderimlerinde ve
    | URL oluşturmada kullanılır.
    |
    */
    'url' => 'http://myapp.test',

    /*
    |--------------------------------------------------------------------------
    | Application Paths
    |--------------------------------------------------------------------------
    |
    | Uygulama dizin yapılandırması.
    | Bu ayarlar storage_path(), resource_path() gibi
    | helper fonksiyonları tarafından kullanılır.
    |
    */
    'base_path' => dirname(__DIR__),
    'storage_path' => dirname(__DIR__) . '/storage',
    'resource_path' => dirname(__DIR__) . '/resources',

    /*
    |--------------------------------------------------------------------------
    | Application Timezone
    |--------------------------------------------------------------------------
    |
    | Uygulama genelinde kullanılacak varsayılan timezone.
    | Liste: http://php.net/manual/en/timezones.php
    |
    */
    'timezone' => 'Europe/Istanbul',

    /*
    |--------------------------------------------------------------------------
    | Application Locale Configuration
    |--------------------------------------------------------------------------
    |
    | Uygulama dil ayarı. Çoklu dil desteği için kullanılır.
    | Örnek değerler: 'en', 'tr', 'fr', 'de'
    |
    */
    'locale' => 'tr',

    /*
    |--------------------------------------------------------------------------
    | Application Fallback Locale
    |--------------------------------------------------------------------------
    |
    | Seçilen dilde çeviri bulunamazsa kullanılacak yedek dil.
    |
    */
    'fallback_locale' => 'en',

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    |
    | Şifreleme için kullanılacak anahtar.
    | En az 32 karakter uzunluğunda olmalıdır.
    |
    */
    'key' => env('APP_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Autoloaded Service Providers
    |--------------------------------------------------------------------------
    |
    | Otomatik yüklenecek servis sağlayıcıları.
    | Framework başlatıldığında bu sınıflar otomatik yüklenir.
    |
    */
    'providers' => [
        // Framework Service Providers
        Framework\Providers\CoreServiceProvider::class,
        Framework\Providers\RouteServiceProvider::class,
        Framework\Providers\DatabaseServiceProvider::class,

        // Application Service Providers
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    |
    | Sınıf takma adları.
    | Örnek: 'Route' => Framework\Facades\Route::class
    |
    */
    'aliases' => [
        'App' => Framework\Facades\App::class,
        'Auth' => Framework\Facades\Auth::class,
        'Cache' => Framework\Facades\Cache::class,
        'Config' => Framework\Facades\Config::class,
        'DB' => Framework\Facades\DB::class,
        'Log' => Framework\Facades\Log::class,
        'Mail' => Framework\Facades\Mail::class,
        'Route' => Framework\Facades\Route::class,
    ],
];
