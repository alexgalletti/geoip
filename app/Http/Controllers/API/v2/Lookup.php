<?php

namespace App\Http\Controllers\API\v2;

use App\Http\Controllers\Controller;
use GeoIp2\WebService\Client as GeoIp2;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use MaxMind\Db\Reader;

class Lookup extends Controller
{
    public function show(Request $request, $ip)
    {
        $ip = $request->input('ip', trim($ip, '/')) ?: $request->getClientIp();

        $key = sprintf('ip.%s', $ip);

        Cache::get('foo');

        $results = [];

        $reader = new GeoIp2(config('maxmind.user_id'), config('maxmind.license_key'));

        if (!empty($ip)) {

            if (!Cache::has($key)) {
                try {
                    $results = json_decode(json_encode($reader->city($ip)), true);
                    unset($results['maxmind']);
                } catch (\Exception $e) {
                    $results = $e->getMessage();
                }

                Cache::put($key, $results, 43800);
            } else {
                $results = Cache::get($key);
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
