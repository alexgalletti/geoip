<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Contracts\Routing\Middleware;

class Secure implements Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->environment() === 'production' && !$request->secure()) {
            throw new Exception('All requests to this endpoint must be secure!');
        }

        return $next($request);
    }
}
