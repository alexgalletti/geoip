<?php

require_once __DIR__.'/vendor/autoload.php';

use Symfony\Component\HttpKernel\Debug\ErrorHandler;
use Symfony\Component\HttpKernel\Debug\ExceptionHandler;

error_reporting(E_ALL);
ErrorHandler::register();
ExceptionHandler::register();

$app = require_once __DIR__.'/app/start.php';

$app->run();
