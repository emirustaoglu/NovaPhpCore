<?php

namespace Framework\Providers;

use NovaCore\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    protected string $configPath = __DIR__ . '/../../config';

    public function boot(): void
    {
        // Framework özel boot işlemleri
    }
}
