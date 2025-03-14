<?php

namespace NovaPhp\Core;

use Buki\Router\Router;
use NovaPhp\Config\ConfigLoader;
use Valitron\Validator;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class Bootstrap
{
    private $basePath;

    public $router;
    public $view;
    public $validator;

    public function __construct($basePath = "")
    {
        $this->basePath = $basePath;

        ConfigLoader::getInstance()->addConfigPath($this->basePath . "/config");

        $whoops = new Run();
        $whoops->pushHandler(new PrettyPageHandler());

        if (config('app.debug')) {
            $whoops->register();
        }

        $this->router = new Router([
            'base_folder' => $this->basePath,
            'paths' => [
                'controllers' => config('app.paths.controllers'),
                'middlewares' => config('app.paths.middlewares'),
            ],
            'namespaces' => [
                'controllers' => config('app.namespaces.controllers'),
                'middlewares' => config('app.namespaces.middlewares'),
            ],
            'debug' => config('app.debug'),
        ]);

        $this->validator = new Validator($_POST);
        Validator::langDir($this->basePath . "resources/languages");
        Validator::lang(config('app.locale'));
        $this->view = new View($this->validator);

    }

    public function run()
    {
        $this->router->run();
    }

}