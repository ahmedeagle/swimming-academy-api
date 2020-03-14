<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Models\Team;
use App\Models\User;
use App\Traits\GlobalTrait;
use App\Traits\HeroTrait;
use Carbon\Carbon;
use DateTime;
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

    public
    function heroes(Request $request)
    {
        $user = $this->auth('user-api');
        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }
        $heroes = $this->getHeroes($user);
        if (isset($heroes) && $heroes->count() > 0) {
            foreach ($heroes as $hero) {
                $startEndOfWeek = getStartAnEndWeekByDate($hero->created_at);
                $hero->week_start_date = $startEndOfWeek['startWeek'];
                $hero->week_end_date = $startEndOfWeek['endWeek'];
                //unset($hero -> user -> team -> times);
            }

            $heroes = $heroes->groupBy('week_start_date');
            $weeks = [];
            foreach ($heroes as $startWeek => $hero) {
                $obj = new \stdClass();
                $obj->week_start_date = date('Y-m-d', strtotime($startWeek));
                $obj->week_end_date = date('Y-m-d', strtotime($startWeek . "+6 days"));

                foreach ($hero as $her) {
                    unset($her->user_id);
                    unset($her->created_at);
                    unset($her->week_start_date);
                    unset($her->week_end_date);
                    //unset($hero['user'] -> team -> times );
                }
                $obj->heroes = $hero;
                array_push($weeks, $obj);
            }
            $weeks = collect($weeks)->sortByDesc('week_start_date')->values()->all();
            return $this->returnData('weeks', $weeks);
        }
        return $this->returnError('E001', trans('messages.There are no data found'));
    }

    public
    function champions(Request $request)
    {

        $user = $this->auth('user-api');
        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        // $weekStartEnd = currentWeekStartEndDate();
        $champions = $this->getChampions($user);
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
