<?php

namespace App\Traits;

use App\Models\Replay;
use App\Models\Ticket;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait TicketTrait
{

    public function getTicketsByType($actorId, $actorType)
    {
        $ticketable_type = ($actorType == 1) ? 'App\Models\User' : '';
        $ticketable_id = $actorId;
        return Ticket::where('ticketable_type', $ticketable_type)->where('ticketable_id', $ticketable_id)->orderBy('id', 'DESC')->paginate(10);
    }

    public function getUnreadMessagesCount($actorId, $actor_type)
    {
        $ticketable_type = ($actor_type == 1) ? 'App\Models\User' : '';
        $ticketable_id = $actorId;
       return  Replay::where('FromUser', 0)  //from admin
            ->where('seen', '0')
            ->whereHas('Ticket', function ($q) use ($ticketable_type, $ticketable_id) {
                $q->where('ticketable_type', $ticketable_type)->where('ticketable_id', $ticketable_id);
            })
            ->count();
    }
}
