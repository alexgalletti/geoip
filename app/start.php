<?php

use Silex\Application;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Provider\SwiftmailerServiceProvider;
use Igorw\Silex\ConfigServiceProvider;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\ParameterBag;

$app = new Application();

$app->register(new SessionServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new SwiftmailerServiceProvider());
$app->register(new TwigServiceProvider());
$app->register(new MonologServiceProvider());
$app->register(new ConfigServiceProvider(sprintf('%s/configuration/environment.php', __DIR__)));
$app->register(new ConfigServiceProvider(sprintf('%s/configuration/%s.php', __DIR__, getenv('APP_ENV') ?: 'production')));

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
