<?php

namespace App\Http\Controllers\Api\User;

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

    public
    function heroes(Request $request)
    {
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
                $_hero->date =date('Y-m-d',strtotime($_hero->hero->created_at));
                unset($_hero->hero);
            }
            return $this->returnData('heroes', $heroes);
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
