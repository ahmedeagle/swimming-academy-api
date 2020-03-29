<?php

namespace App\Events;

use App\Models\Replay;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Support\Str;

class NewNotification2 implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;

    public $content;

    public $date;
    public $time;
    public $photo;
    public $id;
    public $path;
    public $coachId;

    /**
     * Create a new event instance.
     *
     * @return void
     */

    //user rate coach
    public function __construct($notification = [])
    {
        $this->title = 'تقييم للكابتن ' . $notification['coach_name'];
        $this->content = Str::limit($notification['content'], 70);
        $this->date = date("Y M d", strtotime(Carbon::now()));
        $this->time = date("h:i A", strtotime(Carbon::now()));
        $this->photo = $notification['photo'];
        $this->id = $notification['notification_id'];
        $this-> coachId = $notification['coach_id'];
        $this -> path = route('admin.coaches.view',$notification['coach_id']);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['new-notification'];
    }
}
