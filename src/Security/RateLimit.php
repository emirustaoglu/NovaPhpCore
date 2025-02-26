<?php

namespace NovaCore\Security;

use NovaCore\Cache\CacheInterface;

class RateLimit
{
    private CacheInterface $cache;
    private RateLimitConfig $config;
    
    public function __construct(CacheInterface $cache, RateLimitConfig $config = null)
    {
        $this->cache = $cache;
        $this->config = $config ?? new RateLimitConfig();
    }

    public function attempt(string $key, int $maxAttempts, int $decayMinutes, string $ip = null): bool
    {
        if ($ip !== null) {
            if ($this->config->isWhitelisted($ip)) {
                return true;
            }
            
            if ($this->config->isBlacklisted($ip)) {
                return false;
            }
        }
        
        $key = $this->formatKey($key);
        
        // Distributed locking için
        $lockKey = "lock:{$key}";
        if (!$this->acquireLock($lockKey)) {
            return false;
        }
        
        try {
            // Mevcut istek sayısını al
            $attempts = $this->cache->get($key) ?? 0;
            
            // Limit aşıldı mı kontrol et
            if ($attempts >= $maxAttempts) {
                return false;
            }

            // İstek sayısını artır
            $this->cache->set($key, $attempts + 1, $decayMinutes);
            
            return true;
        } finally {
            $this->releaseLock($lockKey);
        }
    }

    public function attemptByGroup(string $key, string $groupName, string $ip = null): bool
    {
        $group = $this->config->getGroup($groupName);
        if ($group === null) {
            throw new \InvalidArgumentException("Rate limit group '{$groupName}' not found");
        }
        
        return $this->attempt($key, $group['maxAttempts'], $group['decayMinutes'], $ip);
    }

    private function acquireLock(string $key, int $timeout = 3): bool
    {
        $start = microtime(true);
        
        do {
            if ($this->cache->set($key, true, 1)) {
                return true;
            }
            
            usleep(100000); // 100ms bekle
        } while (microtime(true) - $start < $timeout);
        
        return false;
    }

    private function releaseLock(string $key): void
    {
        $this->cache->delete($key);
    }

    public function resetAttempts(string $key): void
    {
        $this->cache->delete($this->formatKey($key));
    }

    public function remaining(string $key, int $maxAttempts): int
    {
        $key = $this->formatKey($key);
        $attempts = $this->cache->get($key) ?? 0;
        return max(0, $maxAttempts - $attempts);
    }

    public function tooManyAttempts(string $key, int $maxAttempts): bool
    {
        $key = $this->formatKey($key);
        $attempts = $this->cache->get($key) ?? 0;
        return $attempts >= $maxAttempts;
    }

    private function formatKey(string $key): string
    {
        return 'rate_limit:' . $key . ':' . $this->getRequestSignature();
    }

    private function getRequestSignature(): string
    {
        return md5(
            implode('|', [
                $_SERVER['REMOTE_ADDR'] ?? '',
                $_SERVER['HTTP_USER_AGENT'] ?? ''
            ])
        );
    }
}
