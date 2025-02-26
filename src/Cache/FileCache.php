<?php

namespace NovaCore\Cache;

use NovaCore\Cache\CacheInterface;

class FileCache implements CacheInterface
{
    private string $cachePath;
    
    public function __construct(string $cachePath = null)
    {
        $this->cachePath = $cachePath ?? sys_get_temp_dir() . '/nova_cache';
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0777, true);
        }
    }

    public function get(string $key)
    {
        $filename = $this->getFilename($key);
        if (!file_exists($filename)) {
            return null;
        }

        $content = file_get_contents($filename);
        $data = unserialize($content);

        if ($data['expiry'] !== 0 && time() > $data['expiry']) {
            $this->delete($key);
            return null;
        }

        return $data['value'];
    }

    public function set(string $key, $value, int $minutes = 0): bool
    {
        $filename = $this->getFilename($key);
        $data = [
            'value' => $value,
            'expiry' => $minutes === 0 ? 0 : time() + ($minutes * 60)
        ];

        return file_put_contents($filename, serialize($data)) !== false;
    }

    public function delete(string $key): bool
    {
        $filename = $this->getFilename($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }
        return true;
    }

    public function increment(string $key, int $value = 1): int
    {
        $current = (int) $this->get($key);
        $new = $current + $value;
        $this->set($key, $new);
        return $new;
    }

    public function exists(string $key): bool
    {
        return file_exists($this->getFilename($key));
    }

    private function getFilename(string $key): string
    {
        return $this->cachePath . '/' . md5($key);
    }
}
