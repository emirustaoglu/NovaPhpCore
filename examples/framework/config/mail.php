<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Mailer
    |--------------------------------------------------------------------------
    |
    | Varsayılan mail gönderim sürücüsü.
    | Desteklenen sürücüler: "smtp", "sendmail", "mailgun", "ses", "log", "array"
    |
    */
    'default' => 'smtp',

    /*
    |--------------------------------------------------------------------------
    | Mailer Configurations
    |--------------------------------------------------------------------------
    |
    | Mail gönderim sürücülerinin ayarları. Her sürücü için ayrı ayarlar yapılabilir.
    |
    | SMTP:
    | - En yaygın kullanılan mail gönderim protokolü
    | - Gmail, Yandex, Outlook gibi servisleri kullanabilir
    | - SSL/TLS desteği
    |
    | Sendmail:
    | - Linux/Unix sistemlerde kullanılan yerel mail sunucusu
    | - Harici SMTP sunucusu gerektirmez
    |
    */
    'mailers' => [
        'smtp' => [
            // Mail sunucu adresi
            'host' => 'smtp.mailtrap.io',
            
            // Mail sunucu port numarası
            // Gmail: 587, Yandex: 587, Outlook: 587
            'port' => 587,
            
            // Şifreleme türü: tls, ssl, null
            'encryption' => 'tls',
            
            // Mail sunucu kullanıcı adı
            'username' => 'myusername',
            
            // Mail sunucu şifresi
            'password' => 'mypassword',
            
            // Bağlantı zaman aşımı (saniye)
            'timeout' => 5,
            
            // Debug modu (1 = hataları göster, 0 = gizle)
            'debug' => 0
        ],

        'sendmail' => [
            'path' => '/usr/sbin/sendmail -bs',
            'timeout' => 5
        ],

        'mailgun' => [
            'domain' => 'your-domain.com',
            'secret' => 'your-mailgun-key',
            'endpoint' => 'api.mailgun.net'
        ],

        'ses' => [
            'key' => 'your-ses-key',
            'secret' => 'your-ses-secret',
            'region' => 'us-east-1'
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Global "From" Address
    |--------------------------------------------------------------------------
    |
    | Mail gönderimlerinde kullanılacak varsayılan gönderici bilgileri.
    | Her mail gönderiminde ayrıca belirtilmediği sürece bu değerler kullanılır.
    |
    */
    'from' => [
        'address' => 'no-reply@myapp.test',
        'name' => 'My App'
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail Template Settings
    |--------------------------------------------------------------------------
    |
    | Mail şablonları ile ilgili ayarlar.
    |
    */
    'template' => [
        // Şablon dizini
        'path' => resource_path('views/emails'),
        
        // Varsayılan şablon
        'default' => 'layouts.email',
        
        // Şablon önbelleği
        'cache' => false
    ],

    /*
    |--------------------------------------------------------------------------
    | Mail Queue Settings
    |--------------------------------------------------------------------------
    |
    | Mail kuyruğu ayarları.
    | Maillerin arka planda gönderilmesi için kullanılır.
    |
    */
    'queue' => [
        // Kuyruk kullanımı
        'enabled' => true,
        
        // Kuyruk kanalı
        'channel' => 'emails',
        
        // Öncelik (düşük sayı = yüksek öncelik)
        'priority' => 3
    ]
];
