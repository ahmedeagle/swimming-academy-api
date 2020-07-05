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
        $user = User::with(['academy' => function ($q) {
            $q->select('academies.id', 'academies.name_' . app()->getLocale() . ' as name','academies.name_ar','academies.name_en', 'academies.code', 'academies.logo');
        }, 'team' => function ($q) {
            $q->select('teams.id', 'teams.coach_id', 'teams.name_' . app()->getLocale() . ' as name','teams.name_ar','teams.name_en', 'teams.photo');
            $q->with(['coach' => function ($qq) {
                $qq->select('id', 'name_' . app()->getLocale() . ' as name');
            }]);
        }, 'category' => function ($q) {
            $q->select('categories.id', 'categories.name_' . app()->getLocale() . ' as name');
        }, 'AcademySubscriptions' => function ($q) {
            $q->where('academysubscriptions.status', 1);
            $q->select('id', 'user_id', 'start_date', 'end_date', 'price');
        }])->where('users.mobile', $mobile)->first();

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
        $user = User::where('api_token', $token)->first();
        return $user;
    }

    public function getUserMobileOrEmail($mobile = "")
    {
        $user = null;
        $user = User::where('mobile', $mobile)->first();
        return $user;
    }

    public function getAllData($id)
    {
        $user = User::with(['academy' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'),'name_ar','name_en','code', 'logo');
        }, 'team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'),'name_ar','name_en', 'photo');
        }, 'category' => function ($qq) {
            $qq->select('id', 'name_' . app()->getLocale() . ' as name');
        }])->find($id);

        return $user;
    }

}
