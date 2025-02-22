<?php
// config/logger.php

return [
    // Hangi log seviyelerinin aktif olacağı
    'log_levels' => [
        'info',    // Bilgilendirme logları
        'warning', // Uyarı logları
        'error',   // Hata logları
        'critical',// Kritik loglar
    ],

    // Log dosyalarının kaydedileceği ana dizin
    'log_directory' => __DIR__ . '/../../storage/logs/',

    // Dosya başına maksimum boyut (örneğin 5MB)
    'max_log_size' => 5242880, // 5MB

    // Log dosyalarının nasıl isimlendirileceği
    'log_filename_pattern' => '{level}-{date}.log',

    // İzin verilen log seviyeleri (info, warning, error, critical)
    'log_levels_priority' => [
        'critical' => 1,
        'error' => 2,
        'warning' => 3,
        'info' => 4
    ],
];
