<?php

namespace App\Http\Middleware;

use Closure;

class requestToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $token = $request->get('requestToken', '');

        if ($token != env('REQUEST_TOKEN')) {
            return apiError('对不起，您不在我们的白名单之内', [], 505);
        }

        return $next($request);
    }
}
