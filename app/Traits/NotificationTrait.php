<?php

namespace App\Traits;

use App\Models\Notification;
use App\Models\Ticket;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait NotificationTrait
{
    public function getNotificationsByType($actorId, $actorType)
    {
        $notificationable_type = ($actorType == 1) ? 'App\Models\User' : '';
        $notificationable_id = $actorId;
        //mark all unseen as read
        $unSeenNotifications = Notification::where([
            ['notificationable_type', $notificationable_type],
            ['notificationable_id', $notificationable_id],
            ['seenByUser', '0'],
        ])->get();

        if (isset($unSeenNotifications) && $unSeenNotifications->count() > 0) {
            foreach ($unSeenNotifications as $unSeenNotification) {
                $unSeenNotification->update(['seenByUser' => '1']);
            }
        }

        return Notification::selection()->where('notificationable_type', $notificationable_type)->where('notificationable_id', $notificationable_id)->orderBy('id', 'DESC')->paginate(10);
    }

    public function getUnseenNotificationsCount($actorId, $actorType)
    {
        $notificationable_type = ($actorType == 1) ? 'App\Models\User' : '';
        $notificationable_id = $actorId;

        return $unSeenNotifications = Notification::where([
            ['notificationable_type', $notificationable_type],
            ['notificationable_id', $notificationable_id],
            ['seenByUser', '0'],
        ])->count();
    }

}
