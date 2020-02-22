<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\AcadSubscription;
use App\Models\Activity;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Rate;
use App\Models\Subscription;
use App\Models\Time;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait SubscriptionTrait
{

    //application memberships
    public function PreviousMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo');
        }])
            ->expired()
            ->where('user_id', $user->id)
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->paginate(10);
    }

    public function CurrentMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo');
        }])
            ->current()
            ->where('user_id', $user->id)
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->paginate(10);
    }


    public function allMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo');
        }])
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->where('user_id', $user->id)
            ->orderBy('end_date', 'DESC')
            ->paginate(10);
    }

    //academy memberships

    public function PreviousAcademyMemberShip(User $user)
    {
        $attendance = "(SELECT count(id) FROM attendance WHERE attendance.subscription_id = academysubscriptions.id AND attendance.attend = 1 ) AS attendanceCount";

        return AcadSubscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo', 'quotas');
        }])
            ->expired()
            ->where('user_id', $user->id)
            ->select('id', 'team_id', 'start_date', 'end_date', DB::raw($attendance))
            ->orderBy('end_date', 'DESC')
            ->paginate(10);
    }


    public function CurrentAcademyMemberShip(User $user)
    {
        return AcadSubscription::current()
            ->where('user_id', $user->id)
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->orderBy('end_date', 'DESC')
            ->first();
    }


    public function getTeamTimes($teamId)
    {
        return $times = Time::where('team_id', $teamId)->pluck('day_name');
    }

    public function addUserAttendanceToEachDay($subscriptionDays, $userAttendanceDays)
    {

    }

    public function checkIfDateRated($date, $coachId, $teamId, $userId)
    {
        $rated = Rate::where([
            ['coach_id', $coachId],
            ['team_id', $teamId],
            ['user_id', $userId],
            ['rateable', 0],
            ['date', $date],
        ])->first();

        if ($rated)
            return 1;
        else
            return 0;
    }
}
