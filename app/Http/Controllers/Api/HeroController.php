<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Models\Team;
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
        $messages = [
            'type.required' => 'لابد من ادحال النوع اولا .',
            'type.in' => ' النوع المدخل غير صحيح .',
            'team_id.required' => 'رقم الفريق مطلوب ',
            'team_id.exists' => 'ألفريق غير موجود لدينا '
        ];

        $rules = [
            'type' => 'required|in:teams,users'
        ];

        if ($request->has('type') && $request->type == 'users') {
            $rules['team_id'] = 'required|exists:teams,id';
        }

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $weekStartEnd = currentWeekStartEndDate();
        $teamId = $request->has('team_id') ? $request->team_id : null;
        $results = $this->getHeroes($request->type, $weekStartEnd, $teamId);

        if (count($results) > 0) {
            $total_count = $results->total();
            $results->getCollection()->each(function ($result) {
                return $result;
            });
            $results = json_decode($results->toJson());
            $resultsJson = new \stdClass();
            $resultsJson->current_page = $results->current_page;
            $resultsJson->total_pages = $results->last_page;
            $resultsJson->total_count = $total_count;
            $resultsJson->data = $results->data;
            return $this->returnData('results', $resultsJson);
        }
        return $this->returnError('E001', trans('messages.There are no data found'));
    }
}
