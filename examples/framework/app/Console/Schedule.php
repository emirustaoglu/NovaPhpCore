<?php

use NovaCore\Console\Scheduling\Schedule;

/**
 * Schedule.php Örnek Kullanımı
 * 
 * Bu dosyayı app/Console/Schedule.php konumuna kopyalayın ve ihtiyacınıza göre düzenleyin.
 * 
 * Crontab'a eklenecek komut:
 * * * * * cd /path/to/project && php nova schedule:run >> /dev/null 2>&1
 */

function schedule(Schedule $schedule)
{
    // Mail kuyruğunu her dakika işle
    $schedule->command('mail:process')
        ->everyMinute()
        ->withoutOverlapping()
        ->onFailure(function () {
            // Mail gönderimi başarısız olduğunda yapılacak işlemler
            error_log('Mail queue processing failed');
        });

    // Başarısız mailleri her saat listele ve log dosyasına yaz
    $schedule->command('mail:failed')
        ->hourly()
        ->after(function () {
            // Komut çalıştıktan sonra yapılacak işlemler
            file_put_contents(
                'storage/logs/failed-mails.log',
                date('Y-m-d H:i:s') . ' - Failed mails listed' . PHP_EOL,
                FILE_APPEND
            );
        });

    // Her gün gece yarısı mail kuyruğunu temizle
    $schedule->command('mail:clear')
        ->daily()
        ->when(function () {
            // Sadece belirli bir koşul sağlandığında çalıştır
            return date('N') < 6; // Hafta içi günlerde
        });

    // Her 15 dakikada bir shell komutu çalıştır
    $schedule->exec('php -v')
        ->everyFifteenMinutes()
        ->before(function () {
            // Komut çalışmadan önce yapılacak işlemler
        });

    // Her ay başında özel bir fonksiyon çalıştır
    $schedule->call(function () {
        // Özel işlemler
        return true;
    })
    ->monthly()
    ->evenInMaintenanceMode(); // Bakım modunda bile çalıştır
}
