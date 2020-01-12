<?php

namespace App\Traits\Dashboard;

use App\Http\Controllers\PushNotificationController;
use App\Models\Admin;
use App\Models\City;
use App\Models\Coach;
use App\Models\District;
use App\Models\Doctor;
use App\Models\InsuranceCompany;
use App\Models\Manager;
use App\Models\Nationality;
use App\Models\Nickname;
use App\Models\Notification;
use App\Models\PromoCodeCategory;
use App\Models\Provider;
use App\Models\ProviderType;
use App\Models\Reservation;
use App\Models\Specification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use DB;
use PhpParser\Comment\Doc;

trait PublicTrait
{
    function getRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $chkCode = Admin::where('activation_code', $string)->first();
        if ($chkCode) {
            $this->getRandomString(6);
        }
        return $string;
    }

    public function uploadImage($folder, $image)
    {
        $image->store('/', $folder);
        $filename = $image->hashName();
        $path = 'images/' . $folder . '/' . $filename;
        return $path;
    }


    public function saveNotification(User $user, $notif_data = [])
    {

        $title = $notif_data['title'];
        $ticketId = $notif_data['id'];
        Notification::create([
            "title_ar" => "هناك رد علي التذكره الخاصة بكم" . " - ( $title )",
            "title_en" => "Reply On Your Ticket" . " - ( $title )",
            "content_ar" => "لقد قامت الادارة علي الرد علي التذكره الخاصة بكم  ",
            "content_en" => "The administration replied on  your ticket",
            "notification_type" => 1,
            "notification" => $user->user_id,
            "notificationable_type" => "App\Models\User",
            "notificationable_id" => $user->id,
            "action_id" => $ticketId
        ]);
    }

    public function sendPushNotification(User $user, $notif_data = [])
    {
        (new PushNotificationController($notif_data))->sendUser($user);
    }

    public function authCoachByMobile($mobile, $password)
    {
        $coachID = null;
        $coach = Coach::where('mobile', $mobile)->first();
        $token = Auth::guard('coach-api')->attempt(['mobile' => $mobile, 'password' => $password]);
        if (!$coach)
            return null;

        // to allow open  app on more device with the same account
        if ($token) {
            $newToken = new \App\Models\Token(['coach_id' => $coach->id, 'api_token' => $token]);
            $coach->tokens()->save($newToken);
            //last access token
            $coach->update(['api_token' => $token]);
            return $coach;
        }

        return null;
    }

}
