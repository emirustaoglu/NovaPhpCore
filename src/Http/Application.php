<?php

namespace NovaCore\Http;

use NovaCore\Config\ConfigLoader;
use NovaCore\Database\DB;
use NovaCore\Router\Router;

class Application
{
    protected string $basePath;
    protected Router $router;
    protected static ?Application $instance = null;

    public function __construct(string $basePath)
    {
        $this->basePath = $basePath;
        $this->router = new Router();
        static::$instance = $this;

        $this->bootstrap();
    }

    protected function bootstrap(): void
    {
        // Konfigürasyon yükleme
        $config = ConfigLoader::getInstance();
        $config->load($this->basePath . '/config');

        // Veritabanı bağlantısı
        DB::connect($config->get('database'));
    }

    public function handle(Request $request): Response
    {
        try {
            // Route eşleştirme
            $route = $this->router->match($request);
            
            // Controller çalıştırma
            $response = $route->run($request);
            
            return $response instanceof Response ? $response : new Response($response);
        } catch (\Exception $e) {
            return new Response($e->getMessage(), 500);
        }
    }

    public static function getInstance(): self
    {
        if (static::$instance === null) {
            throw new \RuntimeException('Application has not been initialized');
        }

        return static::$instance;
    }

    public function getBasePath(): string
    {
        return $this->basePath;
    }

    public function getRouter(): Router
    {
        return $this->router;
    }
}
