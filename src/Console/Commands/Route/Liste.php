<?php

namespace NovaPhp\Core\Console\Commands\Route;

use Buki\Router\Router;
use NovaPhp\Core\Console\Command;

class Liste extends Command
{
    protected string $signature = 'route:list';
    protected string $description = 'Projenizde tanımlı route listesini döner.';

    public function handle(): void
    {
        global $app; // Router nesnesini çekiyoruz

        if (!$app->router instanceof Router) {
            $this->error("Router tanımlı değil.");
            return;
        }

        $routes = $app->router->getRoutes();

        if (empty($routes)) {
            $this->info("Tanımlı rota bulunamadı.");
            return;
        }

        $this->info("Tanımlı Rotalar:");
        $this->info(str_pad("Method", 10) . str_pad("URI", 50) . "Controller@Method");
        $this->info(str_repeat("-", 80));
        $i = 0;
        foreach ($routes as $route) {
            if ($i == 0) {
                $this->info(str_pad($route['method'], 10) . str_pad($route['route'], 50) . $route['callback']);
                $i = 1;
            }else{
                $this->warn(str_pad($route['method'], 10) . str_pad($route['route'], 50) . $route['callback']);
                $i = 0;
            }

        }
    }
}