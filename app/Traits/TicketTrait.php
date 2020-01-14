<?php

namespace App\Traits;

use App\Models\Ticket;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait TicketTrait
{

    public function getTicketsByType($actorId, $actorType)
    {
        $ticketable_type = ($actorType == 1) ? 'App\Models\User' : '';
        $ticketable_id =   $actorId ;
        return Ticket::where('ticketable_type', $ticketable_type)->where('ticketable_id', $ticketable_id)->orderBy('id', 'DESC')->paginate(10);
    }
}
