<?php

namespace App\Notifications;

use DouglasResende\FCM\Messages\FirebaseMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PushNotification extends Notification
{
    use Queueable;

    protected $device_token;
    protected $title;
    protected $body;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Array $data)
    {
        $this->device_token = $data['device_token'];
        $this->title = $data['title'];
        $this->body = $data['body'];
    }

    public function via($notifiable)
    {
        return ['fcm'];
    }

    public function toFcm($notifiable)
    {
        $fm = new FirebaseMessage();
        return ($fm)->setContent($this->title,$this->body);
    }
}
