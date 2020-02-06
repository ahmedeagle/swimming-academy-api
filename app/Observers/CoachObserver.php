<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Subscription;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CoachObserver
{


    public function updating(Coach $coach)
    {

    }


    /**
     * Listen to the Entry deleting event.
     *
     * @param Category $category
     * @return void
     */
    public function deleting(Coach $coach)
    {
        Subscription::whereHas('team', function ($q) use ($coach) {
            $q->whereHas('coach', function ($qq) use ($coach) {
                $qq->where('id', $coach->id);
            });
        });
        $coach -> teams() -> delete();
    }

    /**
     * Listen to the Entry saved event.
     *
     * @param Category $category
     * @return void
     */
    public function saved(Coach $coach)
    {
        // Removing Entries from the Cache
        $this->clearCache($coach);
    }

    /**
     * Listen to the Entry deleted event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(Coach $coach)
    {
        // Removing Entries from the Cache
        $this->clearCache($coach);
    }

    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $category
     */
    private function clearCache($coach)
    {
        Cache::flush();
    }
}
