<?php

namespace App\Http\Middleware;

use App\Models\Provider;
use App\Traits\GlobalTrait;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckCoachToken
{
    use GlobalTrait;

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = null;
        try {
            $user = $this->auth('provider-api');
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E331', trans('Unauthenticated'/*'Token is Invalid.'*/));
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E331', trans('Unauthenticated'/*'Token is Expired.'*/));
            }else{
                return $this->returnError('E331', trans('Unauthenticated'/*Authorization Token not found.*/));
            }
        } catch (\Throwable $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E331', trans('Unauthenticated'/*'Token is Invalid.'*/));
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E331', trans('Unauthenticated'/*'Token is Expired.'*/));
            }else{
                return $this->returnError('E331', trans('Unauthenticated'/*Authorization Token not found.*/));
            }
        }
        if(!$user)
            return $this->returnError('E331', trans('Unauthenticated'));

        return $next($request);
    }
}
