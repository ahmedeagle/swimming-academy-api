<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Event;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CategoryObserver
{


    public function updating(Category $category)
    {
        //  if activated/notactive activate all   teams - events - activities - users - coaches - heroes - mosabkat
        $category->teams()->update(['teams.status' => $category->status]);
        $category->activities()->update(['activities.status' => $category->status]);
        $category->events()->update(['events.status' => $category->status]);
        $category->allUsers()->update(['users.status' => $category->status]);
        $category->coaches()->update(['coahes.status' => $category->status]);
    }


    /**
     * Listen to the Entry deleting event.
     *
     * @param Category $category
     * @return void
     */
    public function deleting(Category $category)
    {
         $category->teams()->delete();
         $category->allUsers()->delete();
         $category->activities()->delete();
         $category->events()->delete();
         $category->heroes()->delete();
         $category->champions()->delete();
         $category->coaches()->delete();
    }

    /**
     * Listen to the Entry saved event.
     *
     * @param Category $category
     * @return void
     */
    public function saved(Category $category)
    {
        // Removing Entries from the Cache
        $this->clearCache($category);
    }

    /**
     * Listen to the Entry deleted event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(Category $category)
    {
        // Removing Entries from the Cache
        $this->clearCache($category);
    }

    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $category
     */
    private function clearCache($category)
    {
        Cache::flush();
    }
}
