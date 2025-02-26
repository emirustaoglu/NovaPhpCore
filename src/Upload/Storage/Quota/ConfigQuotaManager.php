<?php

namespace NovaCore\Upload\Storage\Quota;

use NovaCore\Upload\Contracts\QuotaManagerInterface;

class ConfigQuotaManager implements QuotaManagerInterface
{
    private array $config;
    private array $usedSpace = [];

    public function __construct(array $config = [])
    {
        $this->config = array_merge([
            'enabled' => false,
            'default_limit' => null,  // null = sınırsız
            'entities' => []          // ['firma_1' => 1024*1024*1024] gibi
        ], $config);
    }

    public function checkQuota(int $fileSize): bool
    {
        if (!$this->config['enabled']) {
            return true;
        }

        $stats = $this->getStorageStats();
        return $fileSize <= $stats['remaining'];
    }

    public function updateUsedSpace(int $fileSize): void
    {
        if (!$this->config['enabled']) {
            return;
        }

        $this->usedSpace[] = $fileSize;
    }

    public function getStorageStats(): array
    {
        if (!$this->config['enabled']) {
            return [
                'total' => PHP_INT_MAX,
                'used' => 0,
                'remaining' => PHP_INT_MAX
            ];
        }

        $total = $this->config['default_limit'] ?? PHP_INT_MAX;
        $used = array_sum($this->usedSpace);

        return [
            'total' => $total,
            'used' => $used,
            'remaining' => $total - $used
        ];
    }
}
