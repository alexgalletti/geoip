<?php

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

$app = new Application(require_once(__DIR__.'/config.php'));

$app->error(function (\Exception $e, $code) use($app) {
    return $app->json(['result' => false, 'error' => $e->getMessage()], $code);
});

$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
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

require_once __DIR__.'/language.php';
require_once __DIR__.'/helpers.php';
require_once __DIR__.'/routes.php';

$app->run();
