<?php

namespace NovaCore\Console\Commands\Route;

use NovaCore\Console\Command;
use NovaCore\Router\Router;

class Liste extends Command
{
    protected string $signature = 'route:list';
    protected string $description = 'Projenizde tanımlı route listesini döner.';

    public function handle(): void
    {
        global $router; // Router nesnesini çekiyoruz

        if (!$router instanceof Router) {
            $this->error("Router tanımlı değil.");
            return;
        }

        $routes = $router->getRoutes();

        if (empty($routes)) {
            $this->info("Tanımlı rota bulunamadı.");
            return;
        }

        $this->info("Tanımlı Rotalar:");
        $this->info(str_pad("Method", 10) . str_pad("URI", 30) . "Controller@Method");
        $this->info(str_repeat("-", 60));

        foreach ($routes as $route) {
            $method = implode(',', $route['method']); // GET, POST, vs.
            $uri = $route['pattern']; // Rota URL'si
            $action = is_string($route['callback']) ? $route['callback'] : 'Closure';

            $this->info(str_pad($method, 10) . str_pad($uri, 30) . "$action");
        }
    }
}