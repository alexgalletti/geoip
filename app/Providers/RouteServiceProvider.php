<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->group([
            'middleware' => [
                'App\Http\Middleware\CORS',
                'App\Http\Middleware\Secure',
            ]
        ], function ($router) {
            // API Version 1 - Deprecated
            $router->get('api/1/lookup{ip:.*}', 'App\Http\Controllers\API\v1\Lookup@show');
            $router->get('api/1/metadata', 'App\Http\Controllers\API\v1\Lookup@metadata');
            $router->get('api/v1/lookup{ip:.*}', 'App\Http\Controllers\API\v1\Lookup@show');
            $router->get('api/v1/metadata', 'App\Http\Controllers\API\v1\Lookup@metadata');

            // API Version 2 - Current
            $router->get('api/2/lookup{ip:.*}', 'App\Http\Controllers\API\v2\Lookup@show');
            $router->get('api/v2/lookup{ip:.*}', 'App\Http\Controllers\API\v2\Lookup@show');
        });
    }
}
