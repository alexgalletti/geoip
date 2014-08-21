<?php

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Application(require(__DIR__.'/config.php'));

$app->register(new SessionServiceProvider());
$app->register(new TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
$app->register(new TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new MonologServiceProvider(), array(
    'monolog.logfile' => __DIR__.'/storage/debug.log',
));

$app->before(function () use ($app) {
    $app['translator']->setLocale($app['request']->get('locale'));
});

if (array_key_exists('mysql', $app)) {
    $app['db'] = DB::instance($app['mysql']);
}

require __DIR__.'/language.php';
require __DIR__.'/helpers.php';
require __DIR__.'/routes.php';

$app->run();