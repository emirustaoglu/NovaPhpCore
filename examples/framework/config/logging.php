<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | Varsayılan log kanalı. Bu kanal, özel bir kanal belirtilmediğinde
    | kullanılacak olan varsayılan kanaldır.
    |
    */
    'default' => 'file',

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Her log kanalı için ayrı ayarlar yapılabilir. Her kanalın kendi
    | dizini, dosya adı formatı ve log seviyeleri olabilir.
    |
    | Desteklenen log seviyeleri:
    | - emergency: Sistem kullanılamaz durumda
    | - alert: Acil eylem gerekiyor
    | - critical: Kritik durumlar
    | - error: Hata durumları
    | - warning: Uyarı durumları
    | - notice: Normal ama önemli durumlar
    | - info: Bilgilendirme mesajları
    | - debug: Detaylı debug bilgileri
    |
    */
    'channels' => [
        'file' => [
            // Log dosyalarının saklanacağı dizin
            'log_directory' => storage_path('logs'),
            
            // Hangi seviyelerdeki loglar kaydedilecek
            'log_levels' => ['error', 'warning', 'info', 'debug'],
            
            // Log dosya adı formatı
            // Kullanılabilir değişkenler: {level}, {date}
            'log_filename_pattern' => '{level}-{date}.log',
            
            // Maksimum log dosyası boyutu (byte)
            // Varsayılan: 5MB = 5242880 bytes
            'max_log_size' => 5242880,
        ],

        'daily' => [
            'log_directory' => storage_path('logs'),
            'log_levels' => ['error', 'warning', 'info'],
            'log_filename_pattern' => '{date}-nova.log',
            'max_log_size' => 10485760, // 10MB
        ],

        'error' => [
            'log_directory' => storage_path('logs/errors'),
            'log_levels' => ['emergency', 'alert', 'critical', 'error'],
            'log_filename_pattern' => 'error-{date}.log',
            'max_log_size' => 20971520, // 20MB
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Format
    |--------------------------------------------------------------------------
    |
    | Log girdilerinin formatı.
    | Varsayılan format: [timestamp] LEVEL: message {context}
    | Örnek: [2025-02-26 14:15:23] ERROR: Database connection failed {"attempt":3}
    |
    */
    'format' => [
        'timestamp' => 'Y-m-d H:i:s',
        'template' => '[{timestamp}] {level}: {message} {context}'
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Rotation
    |--------------------------------------------------------------------------
    |
    | Log dosyası boyutu max_log_size değerini aştığında,
    | dosya otomatik olarak yeni bir isimle kaydedilir.
    | Yeni dosya adı formatı: original-YYYY-MM-DD-His.log
    |
    */
    'rotation' => [
        'enabled' => true,
        'keep_days' => 14 // Kaç günlük log dosyası tutulacak
    ]
];
