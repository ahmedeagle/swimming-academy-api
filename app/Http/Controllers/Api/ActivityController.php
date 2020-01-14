<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\ActivityTrait;
use App\Traits\EventTrait;
use App\Traits\GlobalTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;
use DB;

class ActivityController extends Controller
{
    use GlobalTrait, ActivityTrait;

    public function __construct(Request $request)
    {

    }

    public
    function activities(Request $request)
    {
        try {
            $activities = $this->getAllActivities();
            if (count($activities) > 0) {
                $total_count = $activities->total();
                $activities->getCollection()->each(function ($activity) {
                    return $activity;
                });
                $activities = json_decode($activities->toJson());
                $activitiesJson = new \stdClass();
                $activitiesJson->current_page = $activities->current_page;
                $activitiesJson->total_pages = $activities->last_page;
                $activitiesJson->total_count = $total_count;
                $activitiesJson->data = $activities->data;
                return $this->returnData('activities', $activitiesJson);
            }

            return $this->returnError('E001', trans('messages.There are no activities found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


}
