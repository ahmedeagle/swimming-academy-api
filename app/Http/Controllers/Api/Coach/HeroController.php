<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Models\Team;
use App\Models\User;
use App\Traits\GlobalTrait;
use App\Traits\HeroTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;
use DB;

class HeroController extends Controller
{
    use GlobalTrait, HeroTrait;

    public function __construct(Request $request)
    {

    }

    //weekly heroes
    public function getTeamsHasHeroes(Request $request)
    {
        $coach = $this->auth('coach-api');
        if (!$coach) {
            return $this->returnError('D000', trans('messages.User not found'));
        }
        $heroes = $this->getTeamHasHero($coach);
        if (count($heroes) > 0) {
            $total_count = $heroes->total();
            $heroes = json_decode($heroes->toJson());
            $heroesJson = new \stdClass();
            $heroesJson->current_page = $heroes->current_page;
            $heroesJson->total_pages = $heroes->last_page;
            $heroesJson->total_count = $total_count;
            $heroesJson->data = $heroes->data;
            return $this->returnData('heroes', $heroesJson);
        }

        return $this->returnError('E001', trans('messages.There are no data found'));
    }

    //weekly heroes
    public function getTeamHeroes(Request $request)
    {
        $messages = [
            "team_id.required" => __('messages.teamRequired'),
            "team_id.exists" => __('messages.teamExists'),
        ];
        $validator = Validator::make($request->all(), [
            "team_id" => "required|exists:teams,id",
        ], $messages);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $coach = $this->auth('coach-api');
        if (!$coach) {
            return $this->returnError('D000', trans('messages.User not found'));
        }
        $heroes = $this->getHeroesByTeamId($request->team_id);
        if (isset($heroes) && $heroes->count() > 0) {
            foreach ($heroes as $hero) {
                $startEndOfWeek = getStartAnEndWeekByDate($hero->created_at);
                $hero->week_start_date = $startEndOfWeek['startWeek'];
                $hero->week_end_date = $startEndOfWeek['endWeek'];
                //unset($hero -> user -> team -> times);
            }
            // $heroes =  $heroes -> keyBy('week_start_date');
            $heroes = $heroes->sortByDesc('week_start_date')->values()->all();
            return $this->returnData('heroes', $heroes);
        }
        return $this->returnError('E001', trans('messages.There are no data found'));
    }

    public
    function heroes(Request $request)
    {
        if ($request->has('type') && $request->type == 'users') {
            $rules['team_id'] = 'required|exists:teams,id';
        }

        $user = $this->auth('user-api');
        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        // $weekStartEnd = currentWeekStartEndDate();
        $heroes = $this->getHeroes($user);
        if (isset($heroes) && $heroes->count() > 0) {
            foreach ($heroes as $_hero) {
                $note = $_hero->hero->{'note_' . app()->getLocale()};
                $_hero->note = $note;
                unset($_hero->hero);
            }
            return $this->returnData('heroes', $heroes);
        }
        return $this->returnError('E001', trans('messages.There are no data found'));
    }


    public
    function champions(Request $request)
    {

        $coach = $this->auth('coach-api');
        if (!$coach) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        // $weekStartEnd = currentWeekStartEndDate();
        $champions = $this->getCoachChampions($coach);
        if (count($champions) > 0) {
            $total_count = $champions->total();

            $champions = json_decode($champions->toJson());
            $championsJson = new \stdClass();
            $championsJson->current_page = $champions->current_page;
            $championsJson->total_pages = $champions->last_page;
            $championsJson->total_count = $total_count;
            $championsJson->data = $champions->data;
            return $this->returnData('champions', $championsJson);
        }
        return $this->returnError('E001', trans('messages.There are no data found'));
    }

}
