<?php

namespace Framework\Providers;

use NovaCore\Support\ServiceProvider;
use NovaCore\Config\ConfigLoader;

class CoreServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Config dizinini belirt
        $this->configs([
            __DIR__ . '/../config'
        ]);
        
        // Configleri yükle
        $this->loadConfigs();
    }
    
    public function boot(): void
    {
        // Config değerlerini kullanma örneği
        $config = ConfigLoader::getInstance();
        
        // Security ayarlarını al
        $csrfEnabled = $config->get('security.csrf.enabled', true);
        $tokenLength = $config->get('security.csrf.token_length', 32);
        
        // Rate limit ayarlarını al
        $rateLimitGroups = $config->get('security.rate_limit.groups', []);
        
        // Runtime'da config değiştirme örneği
        $config->set('security.rate_limit.enabled', false);
    }
}
