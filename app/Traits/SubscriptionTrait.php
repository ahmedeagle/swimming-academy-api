<?php

namespace App\Traits;

use App\Models\Academy;
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

    public function PreviousMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'),'photo');
        }])
            ->expired()
            ->select('id','team_id','start_date', 'end_date', 'attendances')
            ->paginate(10);
    }
    public function CurrentMemberShip(User $user)
    {
        return Subscription::with(['team' => function ($q) {
            $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'photo');
        }])
            ->current()
            ->select('id','team_id','start_date', 'end_date', 'attendances')
            ->paginate(10);
    }

}
