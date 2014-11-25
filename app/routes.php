<?php

use Silex\Application;
use MaxMind\Db\Reader;
use Guzzle\Http\Client;

$app->match('api/1/lookup/{ip}', function (Application $app) {

    $ip = $app['request']->get('ip', false);

    if (!file_exists($app['maxmind_database']) || filemtime($app['maxmind_database']) < time()-2592000) {
        $temp = __DIR__.'/storage/database.mmdb.gz';
        try {
            $client = new Client();
            $response = $client->get($app['maxmind_download'])->setResponseBody($temp)->send();
        } catch (Exception $e) {
            return $app->json(['results' => false, 'error' => 'Error downloading geolite database.']);
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
        return $app->json(['results' => false, 'error' => 'Invalid IP address specified.']);
    }

    if (empty($results)) {
        return $app->json(['results' => false, 'error' => 'There is no data for the specified location.']);
    }

    return $app->json(compact('results'));
})->value('ip', false);
