<?php

namespace App\Traits;

use App\Models\Coach;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;


trait CoachTrait
{

    public function authCoachByMobile($mobile, $password)
    {
        $coachID = null;
        $coach = Coach::with(['academy' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'code', 'logo');
        }, 'category' => function ($qq) {
            $qq->select('id', 'name_' . app()->getLocale() . ' as name');
        }])->where('mobile', $mobile)
            ->first();
        $token = Auth::guard('coach-api')->attempt(['mobile' => $mobile, 'password' => $password]);
        if (!$coach)
            return null;

        // to allow open  app on more device with the same account
        if ($token) {
            $newToken = new \App\Models\Token(['coach_id' => $coach->id, 'api_token' => $token]);
            $coach->tokens()->save($newToken);
            //last access token
            $coach->update(['api_token' => $token]);
            return $coach;
        }

        return null;
    }

    public
    function getTeams($coach)
    {
        return $coach->teams()->active()->paginate(10);
    }


    public function getAllData($id)
    {
        $coach = Coach::with(['academy' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'code', 'logo');
        }, 'category' => function ($qq) {
            $qq->select('id', 'name_' . app()->getLocale() . ' as name');
        }])->find($id);

        return $coach;
    }

    public function findCoach($id)
    {
        return Coach::find($id);
    }


}
