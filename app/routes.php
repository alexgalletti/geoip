<?php

use Silex\Application;
use MaxMind\Db\Reader;

$app->match('api/1/{ip}', function (Application $app) {

    $ip = $app['request']->get('ip', false);

    try {
        $reader = new Reader(__DIR__.'/storage/database.mmdb');

        $results = $reader->get($ip);

        $reader->close();
    } catch (Exception $e) {
        return $app->json(['results' => false, 'error' => 'Invalid IP address specified.']);
    }

    if (empty($results)) {
        return $app->json(['results' => false, 'error' => 'There is no data for the specified location.']);
    }

    return $app->json(compact('results'));
})->value('ip', false);
