<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Traits\GlobalTrait;
use Closure;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class CheckUserStatus
{
    use GlobalTrait;

    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = $this->auth('user-api');
       /* if (!$user->status)
            return $this->returnError('E331', trans('messages.underRevision'));*/
        if (!$user->subscribed)
            return $this->returnError('E338', trans('messages.unsubscribe'));

        return $next($request);
    }
}
