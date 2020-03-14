<?php

namespace App\Traits;

use App\Models\Champion;
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
        /* return User::with(['team' => function ($q) {
             $q->select('id', 'name_' . app()->getLocale() . ' as name');
         }, 'hero'])
             ->where('users.team_id', $user->team_id)
             ->whereHas('hero')
             ->select('id', 'team_id', 'name_' . app()
                     ->getLocale() . ' as name', 'photo')->orderBy('id', 'DESC')
             ->limit(3)->get();*/


        return $heros = Hero::whereHas('user')
            ->with(['user' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name', 'photo', 'team_id');
                $q->with(['team' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name', 'photo');
                }]);
            }])
            ->where('team_id', $user->team_id)
            ->select('id', 'user_id', 'hero_photo', 'created_at', DB::raw('IFNULL(note_'.app()->getLocale().', "") AS note'))
            ->get();
        /*  ->groupBy(function ($data) {
            return Carbon::parse($data->created_at, 'Africa/Cairo')
                ->startOfWeek(Carbon::SATURDAY)
                ->format('W');
        });*/
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
            })->paginate(10);
    }

    public function getHeroesByTeamId($teamId)
    {
        /* return User::select('id', 'team_id', 'name_' . app()->getLocale() . ' as name', 'photo')
             ->where('users.team_id', $teamId)
             ->whereHas('hero')
             ->whereHas('hero')
             ->with(['team' => function ($q) {
                 $q->select('id', 'name_' . app()->getLocale() . ' as name', 'level_' . app()->getLocale() . ' as level');
             }])->orderBy('id', 'DESC')
             ->limit(3)
             ->get();*/

        return $heros = Hero::whereHas('user')
            ->with(['user' => function ($q) {
                $q->select('id', 'name_' . app()->getLocale() . ' as name', 'photo', 'team_id');
                $q->with(['team' => function ($q) {
                    $q->select('id', 'name_' . app()->getLocale() . ' as name', 'photo');
                }]);
            }])
            ->where('team_id', $teamId)
            ->select('id', 'user_id', 'hero_photo', 'created_at', DB::raw('IFNULL(note_'.app()->getLocale().', "") AS note'))
            ->get();
    }


    public function getChampions(User $user)
    {
        return Champion::select('id', 'user_id', 'name_' . app()->getLocale() . ' as name', DB::raw('IFNULL(note_'.app()->getLocale().', "") AS note'), 'champion_photo')
            ->whereHas('user', function ($q) use ($user) {
                $q->where('users.category_id', $user->category_id);
            })
            ->with(['user' => function ($qq) {
                $qq->select('id', 'team_id', 'name_' . app()->getLocale() . ' as name', 'photo');
            }])
            ->orderBy('champions.id', 'DESC')
            ->paginate(10);
    }

    public function getCoachChampions(Coach $coach)
    {
        return Champion::select('id', 'user_id', 'name_' . app()->getLocale() . ' as name', DB::raw('IFNULL(note_' . app()->getLocale() . ', "")  as  note'))
            ->whereHas('user', function ($q) use ($coach) {
                $q->where('users.category_id', $coach->category_id);
            })
            ->with(['user' => function ($qq) {
                $qq->select('id', 'team_id', 'name_' . app()->getLocale() . ' as name', 'photo');
            }])
            ->orderBy('champions.id', 'DESC')
            ->paginate(10);
    }
}
