<?php

namespace App\Observers;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Champion;
use App\Models\Hero;
use App\Models\Subscription;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AcademyObserver
{


    public function updating(Academy $academy)
    {
        //  if activated/notactive activate all   teams - events - activities - users - coaches - heroes - mosabkat


        $academy->categories()->update(['categories.status' => $academy->status]);
        $academy->teams()->update(['teams.status' => $academy->status]);
        $academy->activities()->update(['activities.status' => $academy->status]);
        $academy->events()->update(['events.status' => $academy->status]);
        $academy->users()->update(['users.status' => $academy->status]);
        $academy->coaches()->update(['coahes.status' => $academy->status]);

    }


    /**
     * Listen to the Entry deleting event.
     *
     * @param Category $category
     * @return void
     */
    public function deleting(Academy $academy)
    {
        //  if deleted delete also   teams - events - activities - users - coaches - heroes - mosabkat

        Subscription::whereHas('team', function ($q) use ($academy) {
            $q->whereHas('category', function ($qq) use ($academy) {
                $qq->whereHas('academy', function ($qq) use ($academy) {
                    $qq->where('id', $academy->id);
                });
            });
        })->delete();


        Team::whereHas('category', function ($q) use ($academy) {
            $q->whereHas('academy', function ($qq) use ($academy) {
                $qq->where('id', $academy->id);
            });
        })->delete();

        Hero::whereHas('category', function ($q) use ($academy) {
            $q->whereHas('academy', function ($qq) use ($academy) {
                $qq->where('id', $academy->id);
            });
        })->delete();

        Champion::whereHas('category', function ($q) use ($academy) {
            $q->whereHas('academy', function ($qq) use ($academy) {
                $qq->where('id', $academy->id);
            });
        })->delete();

        $academy->activities()->delete();
        $academy->events()->delete();
        $academy->users()->delete();
        $academy->coaches()->delete();
        $academy->categories()->delete();
        $academy->setting()->delete();
    }


    /**
     * Listen to the Entry saved event.
     *
     * @param Category $category
     * @return void
     */
    public function saved(Academy $academy)
    {
        // Removing Entries from the Cache
        $this->clearCache($academy);
    }

    /**
     * Listen to the Entry deleted event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(Academy $academy)
    {
        // Removing Entries from the Cache
        $this->clearCache($academy);
    }

    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $category
     */
    private function clearCache($academy)
    {
        Cache::flush();
    }
}
