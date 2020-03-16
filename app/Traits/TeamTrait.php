<?php

namespace App\Traits;

use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use DB;
use http\Env\Request;
use Illuminate\Support\Facades\Auth;

trait TeamTrait
{

    public
    function getStudentsInTeam($teamId, $request)
    {
        if ($request->queryStr)
            return Team::find($teamId)
                ->users()
                ->where('users.name_ar', 'LIKE', '%' . trim($request->queryStr) . '%')
                ->orWhere('users.name_en', 'LIKE', '%' . trim($request->queryStr) . '%')
                ->with(['team' => function ($city) {
                    $city->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), DB::raw('level_' . app()->getLocale() . ' as level'));
                }])
                ->selectionByLang()
                ->paginate(10);

        else
            return Team::find($teamId)
                ->users()
                ->with(['team' => function ($city) {
                    $city->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), DB::raw('level_' . app()->getLocale() . ' as level'));
                }])
                ->selectionByLang()
                ->paginate(10);
    }

    public function getTeamsByAcademyId($academyId)
    {
        return Team::active()->where('academy_id', $academyId)->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'), DB::raw('level_' . app()->getLocale() . ' as level'))->get();
    }
}
