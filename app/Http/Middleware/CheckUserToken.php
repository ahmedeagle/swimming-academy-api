<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\GlobalTrait;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckUserToken
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
            $user = $this->auth('user-api');
            //JWTAuth::parseToken()->authenticate();
        } catch (\Exception $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E331', trans('UnauthenticatedToken is Invalid.'));
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E331', trans('Unauthenticated Token is Expired.'));
            }else{
                return $this->returnError('E331', trans('Unauthenticated Authorization Token not found.'));
            }
        } catch (\Throwable $e) {
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                return $this->returnError('E331', trans('Unauthenticated Token is Invalid.'));
            }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                return $this->returnError('E331', trans('Unauthenticated Token is Expired.'));
            }else{
                return $this->returnError('E331', trans('Unauthenticated Authorization Token not found.'));
            }
        }

        if(!$user)
            return $this->returnError('E331', trans('Unauthenticated'));
        return $next($request);
    }
}
