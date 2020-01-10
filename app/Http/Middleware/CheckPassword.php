<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Response;

class CheckPassword
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if($request->api_email != env("API_EMAIL") || $request->api_password != env("API_PASSWORD")){
            return response()->json(['message' => 'Unauthenticated.']);
        }

        return $next($request);
    }
}
