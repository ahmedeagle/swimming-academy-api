<?php

namespace App\Traits;

use App\Models\Hero;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait HeroTrait
{

    public function getHeroes($type, $weekStartEnd ,$team_id = null)
    {
        $startWeek = date('Y-m-d', strtotime($weekStartEnd['startWeek']));
        $endWeek = date('Y-m-d', strtotime($weekStartEnd['endWeek']));

        if ($type == 'teams')
            return Team::active()->select('id', 'name_' . app()->getLocale() . ' as name', 'photo')->whereHas('heroes', function ($q) use ($startWeek, $endWeek) {
                $q->whereBetween('heroes.created_at', [$startWeek, $endWeek]);
            })->paginate(10);
        else {
          return  User::active() -> subscribed() ->select('id','name_'.app()->getLocale().' as name','photo') -> where('team_id',$team_id) ->  whereHas('heroes') -> paginate(10);
        }
    }

}
