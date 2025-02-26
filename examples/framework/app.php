<?php

require __DIR__ . '/../../vendor/autoload.php';

use Framework\Providers\CoreServiceProvider;

// Framework service provider'ı başlat
$provider = new CoreServiceProvider();
$provider->register();
$provider->boot();

// Config helper kullanım örnekleri:

// 1. Config değeri alma
$appName = config('app.name');
$dbHost = config('database.connections.mysql.host');
$mailFrom = config('mail.from.address');

// 2. Varsayılan değer ile config alma
$debug = config('app.debug', false);
$timezone = config('app.timezone', 'UTC');

// 3. Config değeri ayarlama
config(['app.debug' => true]);
config(['mail.from.name' => 'New App Name']);

// 4. Tüm config değerlerini alma
$allConfigs = config()->all();

// 5. Config var mı kontrolü
if (config()->has('cache.stores.redis')) {
    // Redis cache kullanılabilir
}

// 6. Database bağlantısı
$db = new \NovaCore\Database\Database();
$connection = $db->connect();

// 7. Logger kullanımı
$logger = new \NovaCore\Logger\Logger();
$logger->info('Application started with name: ' . config('app.name'));

// 8. Mail ayarları
$mailConfig = config('mail.mailers.smtp');
echo "Mail server: {$mailConfig['host']}:{$mailConfig['port']}\n";

// 9. Cache ayarları
$cacheDriver = config('cache.default');
$cacheConfig = config('cache.stores.' . $cacheDriver);
echo "Cache driver: {$cacheDriver}\n";
