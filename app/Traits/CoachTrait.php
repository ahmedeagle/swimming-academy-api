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
        $coach = Coach::where('mobile', $mobile)->first();
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
        return $coach->teams()->active()->with(['academy' => function ($city) {
                $city->select('id',DB::raw('name_' . app()->getLocale() . ' as name'));
            }])-> paginate(10);
    }

    public function findCoach($id)
    {
        return Coach::find($id);
    }


}
