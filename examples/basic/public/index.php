<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

use NovaCore\Config\ConfigLoader;
use NovaCore\Http\Application;
use NovaCore\Router\Router;

// Uygulama başlatma
$app = new Application(dirname(__DIR__));

// Konfigürasyon yükleme
$config = ConfigLoader::getInstance();
$config->load(dirname(__DIR__) . '/config');

// Router başlatma
$router = new Router();

// Route'ları yükle
require_once dirname(__DIR__) . '/routes/web.php';

// Uygulamayı çalıştır
$app->run();
