<?php

namespace App\Events;

use App\Models\Replay;
use App\Models\Ticket;
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
        $this->date = date("Y M d", strtotime($ticket->created_at));
        $this->time = date("h:i A", strtotime($ticket->created_at));
        $this->photo = $ticket->photo;
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
