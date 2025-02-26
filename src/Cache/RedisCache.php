<?php

namespace NovaCore\Cache;

use Predis\Client;
use NovaCore\Cache\CacheInterface;

class RedisCache implements CacheInterface
{
    private Client $redis;
    
    public function __construct(array $config = [])
    {
        $this->redis = new Client($config);
    }

    public function get(string $key)
    {
        $value = $this->redis->get($key);
        if ($value === null) {
            return null;
        }
        return unserialize($value);
    }

    public function set(string $key, $value, int $minutes = 0): bool
    {
        $serialized = serialize($value);
        if ($minutes === 0) {
            return $this->redis->set($key, $serialized) === true;
        }
        return $this->redis->setex($key, $minutes * 60, $serialized) === true;
    }

    public function delete(string $key): bool
    {
        return $this->redis->del($key) > 0;
    }

    public function increment(string $key, int $value = 1): int
    {
        return $this->redis->incrby($key, $value);
    }

    public function exists(string $key): bool
    {
        return $this->redis->exists($key) > 0;
    }
}
