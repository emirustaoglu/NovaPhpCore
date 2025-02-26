<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Upload Storage Settings
    |--------------------------------------------------------------------------
    |
    | Upload işlemleri için genel ayarlar
    |
    */
    'storage' => [
        'driver' => 'local',  // local, s3 vb.
        'path' => storage_path('upload'),
    ],

    /*
    |--------------------------------------------------------------------------
    | File Validation
    |--------------------------------------------------------------------------
    |
    | Dosya doğrulama ayarları
    |
    */
    'validation' => [
        'max_size' => 5 * 1024 * 1024,  // 5MB
        'allowed_types' => [
            'image/jpeg',
            'image/png',
            'image/gif',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Storage Quota
    |--------------------------------------------------------------------------
    |
    | Depolama alanı sınırlamaları
    | 
    | driver: 
    |   - null: sınırlama yok
    |   - config: config dosyasından yönetim
    |   - database: veritabanından yönetim
    |
    */
    'quota' => [
        'driver' => null,
        
        // Config driver ayarları
        'config' => [
            'enabled' => true,
            'default_limit' => 1024 * 1024 * 1024,  // 1GB
            'entities' => [
                // 'firma_1' => 2147483648,  // 2GB
                // 'firma_2' => 5368709120   // 5GB
            ]
        ],

        // Database driver ayarları
        'database' => [
            'quota_table' => 'storage_quotas',
            'usage_table' => 'storage_usage'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Logging
    |--------------------------------------------------------------------------
    |
    | Dosya yükleme kayıtlarının tutulacağı tablo ayarları
    |
    */
    'database' => [
        'enabled' => false,
        'table' => 'uploaded_files',
        'fields' => [
            'id' => 'id',
            'entity_type' => 'entity_type',  // firma, kullanıcı vb.
            'entity_id' => 'entity_id',
            'original_name' => 'original_name',
            'stored_name' => 'stored_name',
            'mime_type' => 'mime_type',
            'size' => 'size',
            'path' => 'path',
            'created_at' => 'created_at'
        ]
    ]
];
