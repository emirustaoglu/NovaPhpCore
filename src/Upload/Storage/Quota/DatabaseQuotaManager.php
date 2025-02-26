<?php

namespace NovaCore\Upload\Storage\Quota;

use NovaCore\Upload\Contracts\QuotaManagerInterface;
use NovaCore\Database\Database;

class DatabaseQuotaManager implements QuotaManagerInterface
{
    private Database $db;
    private ?string $entityType;
    private ?int $entityId;
    private string $quotaTable;
    private string $usageTable;

    public function __construct(
        Database $db,
        ?string $entityType = null,
        ?int $entityId = null,
        string $quotaTable = 'storage_quotas',
        string $usageTable = 'storage_usage'
    ) {
        $this->db = $db;
        $this->entityType = $entityType;
        $this->entityId = $entityId;
        $this->quotaTable = $quotaTable;
        $this->usageTable = $usageTable;
    }

    public function checkQuota(int $fileSize): bool
    {
        // Eğer entity belirtilmemişse sınırsız kabul et
        if (!$this->entityType || !$this->entityId) {
            return true;
        }

        $stats = $this->getStorageStats();
        return $fileSize <= $stats['remaining'];
    }

    public function updateUsedSpace(int $fileSize): void
    {
        if (!$this->entityType || !$this->entityId) {
            return;
        }

        $this->db->query(
            "INSERT INTO {$this->usageTable} 
            (entity_type, entity_id, file_size, created_at) 
            VALUES (:type, :id, :size, :date)",
            [
                'type' => $this->entityType,
                'id' => $this->entityId,
                'size' => $fileSize,
                'date' => date('Y-m-d H:i:s')
            ]
        );
    }

    public function getStorageStats(): array
    {
        // Varsayılan değerler
        $stats = [
            'total' => PHP_INT_MAX,  // Sınırsız
            'used' => 0,
            'remaining' => PHP_INT_MAX
        ];

        if (!$this->entityType || !$this->entityId) {
            return $stats;
        }

        // Kota limitini al
        $quota = $this->db->getRow(
            "SELECT quota_limit FROM {$this->quotaTable} 
            WHERE entity_type = :type AND entity_id = :id",
            [
                'type' => $this->entityType,
                'id' => $this->entityId
            ]
        );

        if ($quota && isset($quota['data']['quota_limit'])) {
            $stats['total'] = (int)$quota['data']['quota_limit'];
        }

        // Kullanılan alanı hesapla
        $usage = $this->db->getRow(
            "SELECT COALESCE(SUM(file_size), 0) as total_used 
            FROM {$this->usageTable} 
            WHERE entity_type = :type AND entity_id = :id",
            [
                'type' => $this->entityType,
                'id' => $this->entityId
            ]
        );

        if ($usage && isset($usage['data']['total_used'])) {
            $stats['used'] = (int)$usage['data']['total_used'];
            $stats['remaining'] = $stats['total'] - $stats['used'];
        }

        return $stats;
    }
}
