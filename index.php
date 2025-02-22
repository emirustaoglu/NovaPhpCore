<?php

require_once __DIR__ . '/vendor/autoload.php';

define('BasePath', __DIR__ ."/");

use NovaCore\Console\CLI;
$a = new CLI();

$a->run();