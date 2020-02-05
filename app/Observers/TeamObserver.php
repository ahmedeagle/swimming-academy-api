<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Event;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class TeamObserver
{


    public function updating(Team $team)
    {
        $team->users()->update(['users.status' => $team->status]);
    }


    /**
     * Listen to the Entry deleting event.
     *
     * @param Category $category
     * @return void
     */
    public function deleting(Team $team)
    {
        $team->heroes()->delete();
        $team->users()->delete();
        Subscription::whereHas('user', function ($q) use ($team) {
            $q->whereHas('team', function ($qq) use ($team) {
                $qq->where('id', $team->id);
            });
        })->delete();
    }

    /**
     * Listen to the Entry saved event.

     * @param Category $category
     * @return void
     */
    public function saved(Team $team)
    {
        // Removing Entries from the Cache
        $this->clearCache($team);
    }

    /**
     * Listen to the Entry deleted event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(Team $team)
    {
        // Removing Entries from the Cache
        $this->clearCache($team);
    }

    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $category
     */
    private function clearCache($team)
    {
        Cache::flush();
    }
}
