<?php

namespace App\Traits;

use App\Models\Coach;
use App\Models\Hero;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait HeroTrait
{

    public function getHeroes(User $user)
    {
        return User::with(['team' => function ($q) {
            $q->select('id', 'name_' . app()->getLocale() . ' as name');
        }])->where('users.team_id', $user->team_id)->whereHas('hero')->active()->subscribed()->select('id', 'team_id', 'name_' . app()->getLocale() . ' as name', 'photo')->orderBy('id', 'DESC')->limit(3)->get();
    }

    public function getTeamHasHero(Coach $coach)
    {
        $weekStartEnd = currentWeekStartEndDate();
        $startWeek = date('Y-m-d', strtotime($weekStartEnd['startWeek']));
        $endWeek = date('Y-m-d', strtotime($weekStartEnd['endWeek']));

        return Team::select('id', 'name_' . app()->getLocale() . ' as name', 'photo', 'level_' . app()->getLocale() . ' as level')
            ->whereHas('coach', function ($q) use ($coach, $startWeek, $endWeek) {
                $q->where('coahes.id', $coach->id);
            })->whereHas('heroes', function ($qq) use ($startWeek, $endWeek) {
                $qq->whereBetween('created_at', [$startWeek, $endWeek]);
            }) -> paginate(10);
    }

    public function getHeroesByTeamId($teamId)
    {
        return User::select('id', 'team_id', 'name_' . app()->getLocale() . ' as name', 'photo') ->where('users.team_id', $teamId)->whereHas('hero')->where('users.team_id', $teamId)->whereHas('hero')-> with(['team' => function ($q) {
            $q->select('id', 'name_' . app()->getLocale() . ' as name','level_' . app()->getLocale() . ' as level');
        }])->orderBy('id', 'DESC')->limit(3)->get();
    }



}
