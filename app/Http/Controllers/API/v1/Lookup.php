<?php

namespace App\Http\Controllers\API\v1;

use MaxMind\Db\Reader;
use GeoIp2\WebService\Client as GeoIp2;
use Guzzle\Http\Client as Guzzle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use ReflectionClass;

class Lookup extends Controller
{
    public function show(Request $request, $ip)
    {
        $ip = $request->input('ip', trim($ip, '/')) ?: $request->getClientIp();

        $temp = storage_path('app/database.mmdb.gz');

        $database = config('maxmind.database');

        if (!file_exists($temp) && (!file_exists($database) || filemtime($database) < time()-2592000)) {
            try {
                $response = (new Guzzle())->get(config('maxmind.download'))->setResponseBody($temp)->send();
            } catch (Exception $e) {
                return response()->json(['ip' => $ip, 'results' => false, 'error' => 'There is an error with the database or server, please try again later.', 'details' => $e->getMessage()], 500);
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
            $reader = new Reader($database);

            $results = $reader->get($ip);

            $reader->close();
        } catch (Exception $e) {
            return response()->json(['ip' => $ip, 'results' => false, 'error' => 'Invalid IP address specified.', 'details' => $e->getMessage()], 400);
        }

        if (empty($results)) {
            return response()->json(['ip' => $ip, 'results' => false, 'error' => 'There is no location data for the specified IP.'], 404);
        }

        return response()->json(compact('ip', 'results'));
    }

    public function metadata()
    {
        $reader = new Reader(config('maxmind.database'));

        $properties = [];

        foreach ((new ReflectionClass($metadata = $reader->metadata()))->getProperties() as $property) {
            $properties[$property->name] = $metadata->{$property->name};
        }

        $reader->close();

        return response()->json(['metadata' => $properties]);
    }
}
