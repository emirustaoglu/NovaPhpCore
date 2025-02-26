<?php

require_once __DIR__ . '/vendor/autoload.php';

use NovaCore\Router\Router;

$router = new Router(array(
    'base_folder' => '',
    'main_method' => '',
    'paths' => [
        'controllers' =>  '',
        'middlewares' =>  '',
    ],
    'namespaces' => [
        'controllers' =>  '',
        'middlewares' => '',
    ],
    'debug' => true
));

$router->get('', function (){
   echo "Test";
});

$router->run();

echo $router->getRoutes();