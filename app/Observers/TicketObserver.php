<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Champion;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Hero;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class TicketObserver
{


    public function updating(Ticket $ticket)
    {

    }


    /**
     * Listen to the Entry deleting event.
     *
     * @param Category $category
     * @return void
     */
    public function deleting(Ticket $ticket)
    {
         $ticket -> replies()->delete();
    }

    /**
     * Listen to the Entry saved event.
     *
     * @param Category $category
     * @return void
     */
    public function saved(Ticket $ticket)
    {
        // Removing Entries from the Cache
        $this->clearCache($ticket);
    }

    /**
     * Listen to the Entry deleted event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(Ticket $ticket)
    {
        // Removing Entries from the Cache
        $this->clearCache($ticket);
    }

    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $category
     */
    private function clearCache($ticket)
    {
        Cache::flush();
    }
}
