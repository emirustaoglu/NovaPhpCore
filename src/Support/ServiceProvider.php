<?php

namespace NovaCore\Support;

use NovaCore\Config\ConfigLoader;

abstract class ServiceProvider
{
    protected string $configPath;
    protected ConfigLoader $config;

    public function __construct()
    {
        $this->config = ConfigLoader::getInstance();
    }

    /**
     * Service provider'ı başlatır
     */
    public function register(): void
    {
        if (isset($this->configPath)) {
            $this->config->addConfigPath($this->configPath);
        }
    }

    /**
     * Service provider'ı yükler
     */
    abstract public function boot(): void;
}
