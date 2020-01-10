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
        // check  idle or active user
        /* if($user ->  token_created_at  && $user ->  token_created_at != null  && \Request::route()->getName() != "user.logout"){
            $now = Carbon::now();
            $from = Carbon::createFromFormat('Y-m-d H:i:s', $user -> token_created_at);
            $diff_in_minutes = $now->diffInMinutes($from);
                 if($diff_in_minutes > 10 ) {
                     $token = '';

                     // for apple account test only
                     if($user -> mobile != '0123456789'){
                         $activationCode = (string)rand(1000, 9999);
                         $user->activation_code = $activationCode;
                     }

                     $user->api_token = $token;
                     $user->token_created_at = null;
                     $user->update();
                     return $this->returnError('E331', trans('Unauthenticated'));
                  }
             $user -> update(['token_created_at' => Carbon::now()]) ;
         }else{
             $user -> update(['token_created_at' => Carbon::now()]) ;
         }*/
        return $next($request);
    }
}
