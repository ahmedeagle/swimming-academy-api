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

class NewMessage implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $title;

    public $message;

    public $date;

    public $time;

    public $photo;

    public $id;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($replay = [])
    {
        $ticket = Ticket::find($replay['ticket_id'])->ticketable;
        $this->title = Str::limit(Ticket::find($replay['ticket_id'])->title, 50);
        $this->message = Str::limit($replay['message'], 70);
        $this->date = date("Y M d", strtotime(Carbon::now()));
        $this->time = date("h:i A", strtotime(Carbon::now()));
        $this->photo = $ticket->photo;
        $this->id = $replay['ticket_id'];

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['new-message'];
    }
}
