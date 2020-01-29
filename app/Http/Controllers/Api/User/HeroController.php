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
        if ($request->has('type') && $request->type == 'users') {
            $rules['team_id'] = 'required|exists:teams,id';
        }

        $user = $this->auth('user-api');
        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        // $weekStartEnd = currentWeekStartEndDate();
        $heroes = $this->getHeroes($user);
        if (isset($heroes) &&  $heroes -> count() > 0) {
            foreach ($heroes as $_hero){
                $note =   $_hero -> hero -> {'note_'.app()->getLocale()};
                $_hero -> note =$note  ;
                unset($_hero -> hero);
            }
            return $this->returnData('heroes', $heroes);
        }
        return $this->returnError('E001', trans('messages.There are no data found'));
    }
}
