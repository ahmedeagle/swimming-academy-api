<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\Activity;
use App\Models\Coach;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait ActivityTrait
{

    public function getAllActivities(User $user)
    {
        return Activity::active()
            ->where('team_id', $user->team_id)
            ->select('id', 'videoLink as link', DB::raw('title_' . $this->getCurrentLang() . ' as title'))
            ->paginate(10);
    }

    public function getAllCoachActivities(Coach $coach)
    {
        return Activity::active()
            ->whereIn('team_id', $coach->teams-> pluck('id'))
            ->select('id', 'videoLink as link', DB::raw('title_' . $this->getCurrentLang() . ' as title'))
            ->paginate(10);
    }

}
