<?php

use Silex\Application;
use MaxMind\Db\Reader;
use GeoIp2\WebService\Client as GeoIp2;
use Guzzle\Http\Client as Guzzle;

$app->match('api/1/lookup/{ip}', function (Application $app) {

    $ip = $app['request']->get('ip') ?: $app['request']->getClientIp();

    $temp = __DIR__.'/storage/database.mmdb.gz';

    if (!file_exists($temp) && (!file_exists($app['maxmind.database']) || filemtime($app['maxmind.database']) < time()-2592000)) {
        try {
            $response = (new Guzzle())->get($app['maxmind.download'])->setResponseBody($temp)->send();
        } catch (Exception $e) {
            return $app->json(['ip' => $ip, 'results' => false, 'error' => 'There is an error with the database or server, please try again later.', 'details' => $e->getMessage()], 500);
        }

        $gz = function_exists('gzopen64') ? 'gzopen64' : 'gzopen';

        $file = $gz($temp, 'rb');
        $out_file = fopen(str_replace('.gz', '', $temp), 'wb');

        while (!gzeof($file)) {
            fwrite($out_file, gzread($file, 4096));
        }

        fclose($out_file);
        gzclose($file);
        unlink($temp);
    }

    try {
        $reader = new Reader($app['maxmind.database']);

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
    $reader = new Reader($app['maxmind.database']);

    $properties = [];

    foreach ((new ReflectionClass($metadata = $reader->metadata()))->getProperties() as $property) {
        $properties[$property->name] = $metadata->{$property->name};
    }

    $reader->close();

    return $app->json(['metadata' => $properties]);
});

$app->match('api/2/lookup/{ip}', function (Application $app) {

    $ip = $app['request']->get('ip') ?: $app['request']->getClientIp();

    if (!file_exists($app['maxmind.cache']) || (filemtime($app['maxmind.cache']) < time()-2592000)) {
        @unlink($app['maxmind.cache']);

        try {
            touch($app['maxmind.cache']);
            file_put_contents($app['maxmind.cache'], json_encode([], JSON_PRETTY_PRINT), LOCK_EX);
        } catch (Exception $e) {
            return $app->json(['ip' => $ip, 'results' => false, 'error' => 'There is an error with the database or server, please try again later.', 'details' => $e->getMessage()], 500);
        }
    }

    $results = [];

    $reader = new GeoIp2($app['maxmind.user_id'], $app['maxmind.license_key']);

    if (!empty($ip)) {
        $cache = json_decode(file_get_contents($app['maxmind.cache']), true);

        if (is_null($cache) || (json_last_error() !== JSON_ERROR_NONE)) {
            return $app->json(['ip' => $ip, 'results' => false, 'error' => 'There was an error loading the cache, please try again later.', 'details' => $e->getMessage()], 500);
        }

        if (!array_key_exists($ip, $cache)) {
            try {
                $results = json_decode(json_encode($reader->city($ip)), true);
                unset($results['maxmind']);
            } catch (Exception $e) {
                $results = $e->getMessage();
            }

            $cache[$ip] = $results;
            file_put_contents($app['maxmind.cache'], json_encode($cache, JSON_PRETTY_PRINT), LOCK_EX);
        } else {
            $results = $cache[$ip];
        }
    }

    if (is_string($results)) {
        return $app->json(['ip' => $ip, 'results' => false, 'error' => $results], 400);
    }

    if (empty($results) || empty($results['location'])) {
        return $app->json(['ip' => $ip, 'results' => false, 'error' => 'There is no location data for the specified IP.'], 404);
    }

    return $app->json(compact('ip', 'results'));
})->value('ip', false);
