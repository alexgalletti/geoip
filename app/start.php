<?php

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

$app = new Application(require(__DIR__.'/config.php'));

$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__.'/views'
]);
$app->register(new MonologServiceProvider(), [
    'monolog.logfile' => __DIR__.'/storage/debug.log'
]);

$app->error(function (\Exception $e, $code) use($app) {
    if ($app['request']->isXmlHttpRequest() || strpos($app['request']->headers->get('Accept'), 'application/json') === 0) {
        return $app->json(['error' => $e->getMessage()], $code);
    }

    return null;
});

$app->before(function (Request $request) {
    if (strpos($request->headers->get('Accept'), 'application/json') === 0) {
        $data = json_decode($request->getContent(), true);
        $request->request->replace(is_array($data) ? $data : array());
    }
});

require(__DIR__.'/routes.php');

return $app;
