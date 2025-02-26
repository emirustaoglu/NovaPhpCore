<?php

namespace NovaCore\Upload\Contracts;

interface QuotaManagerInterface
{
    /**
     * Depolama alanı sınırını kontrol eder
     * 
     * @param int $fileSize Yüklenecek dosya boyutu (byte)
     * @return bool Dosya yüklenebilir mi?
     */
    public function checkQuota(int $fileSize): bool;

    /**
     * Kullanılan depolama alanını günceller
     * 
     * @param int $fileSize Dosya boyutu (byte)
     * @return void
     */
    public function updateUsedSpace(int $fileSize): void;

    /**
     * Depolama alanı istatistiklerini döndürür
     * 
     * @return array [
     *    'total' => Toplam alan (byte),
     *    'used' => Kullanılan alan (byte),
     *    'remaining' => Kalan alan (byte)
     * ]
     */
    public function getStorageStats(): array;
}
