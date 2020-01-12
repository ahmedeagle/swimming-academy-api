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
    function getStudentsInTeam($teamId)
    {
        return Team::find($teamId) -> users() ->with(['team' => function ($city) {
            $city->select('id',DB::raw('name_' . app()->getLocale() . ' as name'));
        }])->  selectionByLang() ->paginate(10);
    }

    public function getTeams(){
        return Team::active() -> select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'))->get();
    }
}
