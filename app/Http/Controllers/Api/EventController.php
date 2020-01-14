<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\EventTrait;
use App\Traits\GlobalTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;
use DB;

class EventController extends Controller
{
    use GlobalTrait, EventTrait;

    public function __construct(Request $request)
    {

    }

    public
    function events(Request $request)
    {
        try {
            $events = $this->getAllEvents();
            if (count($events) > 0) {
                $total_count = $events->total();
                $events->getCollection()->each(function ($event) {
                     return $event;
                });
                $events = json_decode($events->toJson());
                $eventsJson = new \stdClass();
                $eventsJson->current_page = $events->current_page;
                $eventsJson->total_pages = $events->last_page;
                $eventsJson->total_count = $total_count;
                $eventsJson->data = $events->data;
                return $this->returnData('events', $eventsJson);
            }
            return $this->returnError('E001', trans('messages.There are no events found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


}
