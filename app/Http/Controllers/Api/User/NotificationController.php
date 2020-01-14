<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Traits\GlobalTrait;
use App\Traits\NotificationTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;
use DB;

class NotificationController extends Controller
{
    use GlobalTrait, NotificationTrait;

    public function __construct(Request $request)
    {

    }

    public function get_notifications(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "actor_type" => "required|in:1",  // 1 user for now
                "notify_type" => "required|in:list,count",
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $actor_type = $request->actor_type;
            if ($actor_type == 1 or $actor_type == '1') {
                $user = $this->auth('user-api');
                if (!$user) {
                    return $this->returnError('D000', trans('messages.User not found'));
                }

                if ($request->notify_type == 'list')
                    $notifications = $this->getNotificationsByType($user->id, $actor_type);
                else
                {
                    $notificationsCount = $this->getUnseenNotificationsCount($user->id, $actor_type);
                    return $this->returnData('notificationCount', $notificationsCount);
                }


            } else {
                //not other type until now
            }

            if (count($notifications->toArray()) > 0) {
                $total_count = $notifications->total();
                $notifications->getCollection()->each(function ($notification) {
                    unset($notification->notificationable_type);
                    unset($notification->notificationable_id);
                    unset($notification->seen);
                    return $notification;
                });

                $notifications = json_decode($notifications->toJson());
                $notificationsJson = new \stdClass();
                $notificationsJson->current_page = $notifications->current_page;
                $notificationsJson->total_pages = $notifications->last_page;
                $notificationsJson->total_count = $total_count;
                $notificationsJson->data = $notifications->data;
                return $this->returnData('tickets', $notificationsJson);
            }
            return $this->returnError('E001', trans("messages.No notification founded"));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
