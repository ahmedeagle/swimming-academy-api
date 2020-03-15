<?php

namespace App\Traits\Dashboard;

use App\Models\Message;
use App\Models\Ticket;
use Freshbitsweb\Laratables\Laratables;

trait MessageTrait
{
    public function getMessageById($id)
    {
        return Ticket::find($id);
    }

    public function getAllUserMessages()
    {
        return Ticket::where('ticketable_type', 'App\Models\User')->orderBy('created_at', 'DESC')->get();
    }
    public function getAllTickets()
    {
        return Ticket::orderBy('created_at', 'DESC')->get();
    }

    public function getAllCoachMessages()
    {
        return Ticket::where('ticketable_type', 'App\Models\Coach')->orderBy('created_at', 'DESC')->get();
    }

    public function getAllProviderMessages()
    {
        return Laratables::recordsOf(Ticket::class, function ($query) {
            return $query->where('actor_type', 1);
        });
    }

    public function getMessageReplies($id)
    {
        return Ticket::where('ticket_id', $id)->orderBy('created_at')->orderBy('order')->get();
    }

    public function getLastReplyOrder($id)
    {
        $message = Message::where('message_id', $id)->orderBy('order', 'DESC')->first();
        return $message != null ? $message->order : 0;
    }

}
