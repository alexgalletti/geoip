<?php

use Silex\Application;
use MaxMind\Db\Reader;
use Guzzle\Http\Client;

$app->match('api/1/lookup/{ip}', function (Application $app) {

    $ip = $app['request']->get('ip') ?: $app['request']->getClientIp();

    $temp = __DIR__.'/storage/database.mmdb.gz';

    if (!file_exists($temp) && (!file_exists($app['maxmind_database']) || filemtime($app['maxmind_database']) < time()-2592000)) {
        try {
            $response = (new Client())->get($app['maxmind_download'])->setResponseBody($temp)->send();
        } catch (Exception $e) {
            return $app->json(['ip' => $ip, 'results' => false, 'error' => 'There is an error with the database or server, please try again later.', 'details' => $e->getMessage()], 500);
        }

        $file = gzopen($temp, 'rb');
        $out_file = fopen(str_replace('.gz', '', $temp), 'wb');

        while (!gzeof($file)) {
            fwrite($out_file, gzread($file, 4096));
        }

        fclose($out_file);
        gzclose($file);
        unlink($temp);
    }

    try {
        $reader = new Reader($app['maxmind_database']);

        $results = $reader->get($ip);

        $reader->close();
    } catch (Exception $e) {
        return $app->json(['ip' => $ip, 'results' => false, 'error' => 'Invalid IP address specified.', 'details' => $e->getMessage()], 400);
    }

    if (empty($results)) {
        return $app->json(['ip' => $ip, 'results' => false, 'error' => 'There is no location data for the specified IP.'], 404);
    }

    return $app->json(compact('ip', 'results'));
})->value('ip', false);

$app->match('api/1/metadata', function (Application $app) {
    $reader = new Reader($app['maxmind_database']);

    $properties = [];

    foreach ((new ReflectionClass($metadata = $reader->metadata()))->getProperties() as $property) {
        $properties[$property->name] = $metadata->{$property->name};
    }

    $reader->close();

    return $app->json(['metadata' => $properties]);
});
