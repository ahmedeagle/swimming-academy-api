<?php

namespace App\Traits;

use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;


trait UserTrait
{
    public function authUserByMobile($mobile, $password)
    {
        $userId = null;
        $user = User::where('mobile', $mobile)->first();
        $token = Auth::guard('user-api')->attempt(['mobile' => $mobile, 'password' => $password]);
        if (!$user)
            return null;
        // to allow open  app on more device with the same account
        if ($token) {
            $newToken = new \App\Models\UserToken(['user_id' => $user->id, 'api_token' => $token]);
            $user->tokens()->save($newToken);
            //last access token
            $user->update(['api_token' => $token]);
            return $user;
        }
        return null;
    }

    public function getUserByTempToken($token)
    {
        $user = null;
        $user = User::where('api_token',$token )->first();
        return $user;
    }

    public function getUserMobileOrEmail($mobile = "")
    {
        $user = null;
        $user = User::where('mobile', $mobile)->first();
        return $user;
    }
}
