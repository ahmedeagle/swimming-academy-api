<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Provider;
use Carbon\Carbon;

class PushNotificationController extends Controller
{
    protected $title;
    protected $notificationType;
    protected $body;
    protected $id;
    protected const API_ACCESS_KEY_USER = 'AAAAc1Y3kCA:APA91bGJNpIGQQo2LeIbiGzcNZQyITAbyR9zHQXkFKGifEj9cLdvaOy3n8YV8_vLzMPRrY0kUJm2634OUjApRf7PTJ4aj8PHRfZKgyy_05-0JxI7S_5AQ6IMEB9QF_HfG2fybbehpxQL';

    private const fcmUrl = 'https://fcm.googleapis.com/fcm/send';

    //

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Array $data)
    {
        $this->title = $data['title'];
        $this->body = $data['body'];
        $this->notificationType = $data['notification_type'];
        $this->id = $data['id'];
    }

    public function sendUser(User $user)
    {
        // $data['device_token'] = $User->device_token;
        $notification = [
            'title' => $this->title,
            'body' => $this->body,
            "click_action" => "action"
        ];

        $extraNotificationData = [
            'notification_type' => $this->notificationType,
            'id' => $this->id
        ];

        $fcmNotification = [
            'to' => $user->device_token,
            'notification' => $notification,
            'data' => $extraNotificationData
        ];

        return $this->sendFCM($fcmNotification);
    }


    private function sendFCM($fcmNotification)
    {
        $key = self::API_ACCESS_KEY_USER;
        $headers = [
            'Authorization: key=' . $key,
            'Content-Type: application/json'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, self::fcmUrl);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fcmNotification));
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

}
