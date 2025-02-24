<?php

namespace NovaCore\Console\Commands\Route;

use NovaCore\Router\Router;

class Liste
{

    public function handle(): void
    {
        global $router; // Routure nesnesini çekiyoruz

        if (!$router instanceof Router) {
            echo "Router tanımlı değil.\n";
            return;
        }

        $routes = $router->getRoutes();

        if (empty($routes)) {
            echo "Tanımlı rota bulunamadı.\n";
            return;
        }

        echo "Tanımlı Rotalar:\n";
        echo str_pad("Method", 10) . str_pad("URI", 30) . "Controller@Method\n";
        echo str_repeat("-", 60) . "\n";

        foreach ($routes as $route) {
            $method = implode(',', $route['method']); // GET, POST, vs.
            $uri = $route['pattern']; // Rota URL'si
            $action = is_string($route['callback']) ? $route['callback'] : 'Closure';

            echo str_pad($method, 10) . str_pad($uri, 30) . "$action\n";
        }
    }

    public static function getDescription(): string
    {
        return "Projenizde tanımlı routa listesini döner.";
    }
}