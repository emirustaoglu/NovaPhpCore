<?php

namespace NovaCore\Config;

class ConfigLoader
{
    private array $items = [];
    private array $configPaths = [];
    private static ?ConfigLoader $instance = null;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Framework tarafından config dizini ekler
     */
    public function addConfigPath(string $path): self
    {
        if (!in_array($path, $this->configPaths)) {
            $this->configPaths[] = $path;
            $this->loadConfigFiles($path);
        }
        return $this;
    }

    /**
     * Belirtilen dizindeki tüm config dosyalarını yükler
     */
    private function loadConfigFiles(string $path): void
    {
        $path = rtrim($path, '/');
        $files = glob($path . '/*.php');
        
        foreach ($files as $file) {
            $key = basename($file, '.php');
            $config = require $file;
            
            if (isset($this->items[$key])) {
                $this->items[$key] = array_replace_recursive($this->items[$key], $config);
            } else {
                $this->items[$key] = $config;
            }
        }
    }

    /**
     * Config değerini alır
     * Örnek: get('database.connections.mysql.host')
     */
    public function get(string $key, $default = null)
    {
        $segments = explode('.', $key);
        $config = $this->items;

        foreach ($segments as $segment) {
            if (!is_array($config) || !array_key_exists($segment, $config)) {
                return $default;
            }
            $config = $config[$segment];
        }

        return $config;
    }

    /**
     * Config değerini ayarlar
     */
    public function set(string $key, $value): void
    {
        $segments = explode('.', $key);
        $config = &$this->items;

        foreach ($segments as $i => $segment) {
            if ($i === count($segments) - 1) {
                $config[$segment] = $value;
                break;
            }

            if (!isset($config[$segment]) || !is_array($config[$segment])) {
                $config[$segment] = [];
            }

            $config = &$config[$segment];
        }
    }

    /**
     * Tüm config değerlerini döndürür
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Config değerinin var olup olmadığını kontrol eder
     */
    public function has(string $key): bool
    {
        return $this->get($key) !== null;
    }
}
