<?php

namespace NovaCore\Mail\Queue;

use NovaCore\Config\ConfigLoader;
use NovaCore\Mail\Queue\Drivers\DatabaseDriver;
use NovaCore\Mail\Queue\Drivers\FileDriver;
use NovaCore\Mail\Queue\Drivers\RedisDriver;
use NovaCore\Mail\Queue\Drivers\SyncDriver;

class QueueManager
{
    protected array $config;
    protected array $drivers = [];

    public function __construct()
    {
        // Varsayılan config
        $this->config = [
            'driver' => 'sync',
            'table' => 'mail_queue',
            'queue' => 'default',
            'retry_after' => 90,
            'path' => 'storage/mail/queue',
        ];

        // Config dosyasından ayarları yükle (varsa)
        try {
            $configFromFile = ConfigLoader::getInstance()->get('mail.queue');
            if (is_array($configFromFile)) {
                $this->config = array_merge($this->config, $configFromFile);
            }
        } catch (\Exception $e) {
            // Config dosyası yoksa varsayılan ayarları kullan
        }
    }

    public function driver(?string $name = null): QueueDriverInterface
    {
        $name = $name ?? $this->config['driver'] ?? 'sync';

        if (!isset($this->drivers[$name])) {
            $this->drivers[$name] = $this->createDriver($name);
        }

        return $this->drivers[$name];
    }

    protected function createDriver(string $name): QueueDriverInterface
    {
        return match ($name) {
            'sync' => new SyncDriver(),
            'database' => new DatabaseDriver($this->config),
            'redis' => new RedisDriver($this->config),
            'file' => new FileDriver($this->config),
            default => throw new \InvalidArgumentException("Driver [{$name}] not supported.")
        };
    }
}
