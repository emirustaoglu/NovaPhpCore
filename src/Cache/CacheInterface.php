<?php

namespace NovaCore\Cache;

interface CacheInterface
{
    public function get(string $key);
    public function set(string $key, $value, int $minutes = 0): bool;
    public function delete(string $key): bool;
    public function increment(string $key, int $value = 1): int;
    public function exists(string $key): bool;
}
