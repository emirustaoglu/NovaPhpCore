<?php

namespace NovaCore\Router;

use Buki\Router\Router as BukiRouter;

class Router
{
    protected BukiRouter $router;

    public function __construct(array $config)
    {
        $this->router = new BukiRouter([
            'base_folder' => $config['base_folder'] ?? '',
            'main_method' => $config['main_method'] ?? '',
            'paths' => [
                'controllers' => $config['paths']['controllers'] ?? '',
                'middlewares' => $config['paths']['middlewares'] ?? '',
            ],
            'namespaces' => [
                'controllers' => $config['namespaces']['controllers'] ?? '',
                'middlewares' => $config['namespaces']['middlewares'] ?? '',
            ],
            'debug' => $config['debug'] ?? false
        ]);
    }

    public function get(string $path, callable|array $callback): void
    {
        $this->router->get($path, $callback);
    }

    public function post(string $path, callable|array $callback): void
    {
        $this->router->post($path, $callback);
    }

    public function run(): void
    {
        $this->router->run();
    }
}
