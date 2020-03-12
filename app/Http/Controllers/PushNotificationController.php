<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\User;
use App\Models\Provider;
use Carbon\Carbon;

class PushNotificationController extends Controller
{
    protected $device_token;
    protected $title;
    protected $body;
    protected const API_ACCESS_KEY = 'AAAAPpWS3Og:APA91bEEis8JjxkEt6N5vQfATT9YTVdevj0Iaq1DFrG806QljAvx6HDnAnWorjpXAhhCUkjtdZmv2D9lRm3VP-0nUOLF3C0V-XVKOt9GwjJg7RIDAPDHMrU0C2cfQzzZSVRvuzxKCvz1';
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
    }

    public function send($device_token = null)
    {
        $notification = [
            'title' => $this->title,
            'body' => $this->body,
            "click_action" => "action"
        ];


        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to' => $device_token,
            'notification' => $notification,
        ];

        return $this->sendFCM($fcmNotification, 'user');
    }


    public function sendProviderWeb(Provider $notify, $reservation_no = null, $type = 'new_reservation')
    {

        if ($reservation_no != null) {
            $notification = [
                'title' => $this->title,
                'body' => $this->body,
                "reservation_no" => $reservation_no,
                "type" => $type
            ];
        } else {

            $notification = [
                'title' => $this->title,
                'body' => $this->body,
                "type" => $type
            ];
        }

        $notificationData = new \stdClass();
        $notificationData->notification = $notification;
        // $extraNotificationData = ["message" => $notification,"moredata" =>'New Data'];
        $fcmNotification = [
            //'registration_ids' => $tokenList, //multple token array
            'to' => $notify->web_token,//'/topics/alldevices',// $User->device_token, //single token
            'data' => $notificationData

        ];
        return $this->sendFCM($fcmNotification, 'provider');

    }


    /*  // weBrowser Push Format
      public function sendProviderWebBrowser(Provider $notify)
      {

          $notification = [
              'title' => $this->title,
              'body' => $this->body,
          ];

          $notificationData = new \stdClass();
          $notificationData->notification = $notification;
          // $extraNotificationData = ["message" => $notification,"moredata" =>'New Data'];
          $fcmNotification = [
              //'registration_ids' => $tokenList, //multple token array
              'to' => $notify->web_token,//'/topics/alldevices',// $User->device_token, //single token
              'data' => $notificationData

          ];


          $this->sendFCM($fcmNotification, 'provider');
      }*/


    private function sendFCM($fcmNotification, $type = 'user')
    {

        $key = self::API_ACCESS_KEY;
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

    public function setData(Array $data)
    {
        $this->device_token = $data['device_token'];
        $this->title = $data['title'];
        $this->body = $data['body'];
    }
}
