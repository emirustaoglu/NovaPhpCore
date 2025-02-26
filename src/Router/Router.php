<?php

namespace NovaCore\Router;

use Buki\Router\Router as BukiRouter;
use NovaCore\Config\ConfigLoader;
use NovaCore\Http\Request;
use NovaCore\Http\Response;

class Router
{
    protected BukiRouter $router;
    protected array $middlewareGroups = [];
    protected array $routeMiddleware = [];
    protected string $prefix = '';
    protected array $groupStack = [];

    public function __construct()
    {
        $config = ConfigLoader::getInstance()->get('router', []);
        
        $this->router = new BukiRouter([
            'base_folder' => $config['base_folder'] ?? '',
            'main_method' => $config['main_method'] ?? 'main',
            'paths' => [
                'controllers' => $config['paths']['controllers'] ?? 'App\\Controllers',
                'middlewares' => $config['paths']['middlewares'] ?? 'App\\Middlewares',
            ],
            'namespaces' => [
                'controllers' => $config['namespaces']['controllers'] ?? 'App\\Controllers',
                'middlewares' => $config['namespaces']['middlewares'] ?? 'App\\Middlewares',
            ],
            'debug' => $config['debug'] ?? false
        ]);

        $this->loadMiddleware();
    }

    /**
     * Middleware gruplarını ve route middleware'leri yükler
     */
    protected function loadMiddleware(): void
    {
        $config = ConfigLoader::getInstance()->get('middleware', []);
        
        $this->middlewareGroups = $config['groups'] ?? [];
        $this->routeMiddleware = $config['route'] ?? [];
    }

    /**
     * Route grubu oluşturur
     */
    public function group(array $attributes, callable $callback): void
    {
        $this->updateGroupStack($attributes);

        call_user_func($callback, $this);

        array_pop($this->groupStack);
    }

    /**
     * Grup stack'ini günceller
     */
    protected function updateGroupStack(array $attributes): void
    {
        if (!empty($this->groupStack)) {
            $attributes = $this->mergeWithLastGroup($attributes);
        }

        $this->groupStack[] = $attributes;

        // Prefix'i güncelle
        $this->prefix = $this->getGroupPrefix();
    }

    /**
     * Son grup ile yeni attributeleri birleştirir
     */
    protected function mergeWithLastGroup(array $new): array
    {
        $lastGroup = end($this->groupStack);

        return [
            'middleware' => array_merge(
                $lastGroup['middleware'] ?? [],
                $new['middleware'] ?? []
            ),
            'prefix' => trim(
                ($lastGroup['prefix'] ?? '') . '/' . ($new['prefix'] ?? ''),
                '/'
            ),
            'namespace' => trim(
                ($lastGroup['namespace'] ?? '') . '\\' . ($new['namespace'] ?? ''),
                '\\'
            ),
        ];
    }

    /**
     * Mevcut grup prefix'ini alır
     */
    protected function getGroupPrefix(): string
    {
        if (empty($this->groupStack)) {
            return '';
        }

        $lastGroup = end($this->groupStack);
        return $lastGroup['prefix'] ?? '';
    }

    /**
     * GET route'u ekler
     */
    public function get(string $path, $callback): void
    {
        $this->addRoute('GET', $path, $callback);
    }

    /**
     * POST route'u ekler
     */
    public function post(string $path, $callback): void
    {
        $this->addRoute('POST', $path, $callback);
    }

    /**
     * PUT route'u ekler
     */
    public function put(string $path, $callback): void
    {
        $this->addRoute('PUT', $path, $callback);
    }

    /**
     * DELETE route'u ekler
     */
    public function delete(string $path, $callback): void
    {
        $this->addRoute('DELETE', $path, $callback);
    }

    /**
     * Route ekler
     */
    protected function addRoute(string $method, string $path, $callback): void
    {
        // Prefix ekle
        $path = $this->prefix ? '/' . trim($this->prefix, '/') . '/' . trim($path, '/') : $path;
        
        // Middleware'leri hazırla
        $middleware = $this->getRouteMiddleware();

        // Route'u ekle
        $this->router->{strtolower($method)}($path, $callback);

        // Middleware'leri ekle
        if (!empty($middleware)) {
            $this->router->middleware($middleware);
        }
    }

    /**
     * Route middleware'lerini alır
     */
    protected function getRouteMiddleware(): array
    {
        if (empty($this->groupStack)) {
            return [];
        }

        $middleware = [];
        foreach ($this->groupStack as $group) {
            if (isset($group['middleware'])) {
                foreach ((array) $group['middleware'] as $name) {
                    if (isset($this->middlewareGroups[$name])) {
                        $middleware = array_merge(
                            $middleware,
                            $this->middlewareGroups[$name]
                        );
                    } elseif (isset($this->routeMiddleware[$name])) {
                        $middleware[] = $this->routeMiddleware[$name];
                    }
                }
            }
        }

        return array_unique($middleware);
    }

    /**
     * Route'ları çalıştırır
     */
    public function run(): void
    {
        $this->router->run();
    }

    /**
     * Tüm route'ları listeler
     */
    public function getRoutes(): array
    {
        return $this->router->getRoutes();
    }

    /**
     * Route adı verir
     */
    public function name(string $name): self
    {
        $this->router->name($name);
        return $this;
    }

    /**
     * Route middleware ekler
     */
    public function middleware(array|string $middleware): self
    {
        $this->router->middleware($middleware);
        return $this;
    }

    /**
     * URL oluşturur
     */
    public function url(string $name, array $params = []): string
    {
        return $this->router->url($name, $params);
    }
}
