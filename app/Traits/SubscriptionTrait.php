<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\AcadSubscription;
use App\Models\Activity;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Subscription;
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
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->paginate(10);
    }

    public function CurrentMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo');
        }])
            ->current()
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->paginate(10);
    }


    public function allMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo');
        }])
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->orderBy('end_date', 'DESC')
            ->paginate(10);
    }

    //academy memberships

    public function PreviousAcademyMemberShip(User $user)
    {
        return AcadSubscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo', 'quotas');
        }])
            ->expired()
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->orderBy('end_date', 'DESC')
            ->paginate(10);
    }

    public function CurrentAcademyMemberShip(User $user)
    {
        return AcadSubscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo', 'quotas');
        }])
            ->current()
            ->select('id', 'team_id', 'start_date', 'end_date')
            ->orderBy('end_date', 'DESC')
            ->paginate(10);
    }
}
