<?php namespace App\Http\Controllers\API\v2;

use MaxMind\Db\Reader;
use GeoIp2\WebService\Client as GeoIp2;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Lookup extends Controller
{
    public function show(Request $request, $ip)
    {
        $ip = $request->input('ip', trim($ip, '/')) ?: $request->getClientIp();

        $cache = config('maxmind.cache');

        if (!file_exists($cache) || (filemtime($cache) < time()-2592000)) {
            if (file_exists($cache)) {
                unlink($cache);
            }

            try {
                touch($cache);
                file_put_contents($cache, json_encode([], JSON_PRETTY_PRINT), LOCK_EX);
            } catch (Exception $e) {
                return response()->json(['ip' => $ip, 'results' => false, 'error' => 'There is an error with the database or server, please try again later.', 'details' => $e->getMessage()], 500);
            }
        }

        $results = [];

        $reader = new GeoIp2(config('maxmind.user_id'), config('maxmind.license_key'));

        if (!empty($ip)) {
            $cache_data = json_decode(file_get_contents($cache), true);

            if (is_null($cache_data) || (json_last_error() !== JSON_ERROR_NONE)) {
                return response()->json(['ip' => $ip, 'results' => false, 'error' => 'There was an error loading the cache, please try again later.', 'details' => $e->getMessage()], 500);
            }

            if (!array_key_exists($ip, $cache_data)) {
                try {
                    $results = json_decode(json_encode($reader->city($ip)), true);
                    unset($results['maxmind']);
                } catch (\Exception $e) {
                    $results = $e->getMessage();
                }

                $cache_data[$ip] = $results;
                file_put_contents($cache, json_encode($cache_data, JSON_PRETTY_PRINT), LOCK_EX);
            } else {
                $results = $cache_data[$ip];
            }
        }


        if (is_string($results)) {
            return response()->json(['ip' => $ip, 'results' => false, 'error' => $results], 400);
        }

        if (empty($results) || empty($results['location'])) {
            return response()->json(['ip' => $ip, 'results' => false, 'error' => 'There is no location data for the specified IP.'], 404);
        }

        return response()->json(compact('ip', 'results'));
    }
}
