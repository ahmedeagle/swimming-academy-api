<?php

namespace App\Observers;

use App\Models\Category;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Hero;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class UserObserver
{


    public function updating(User $user)
    {

    }


    /**
     * Listen to the Entry deleting event.
     *
     * @param Category $category
     * @return void
     */
    public function deleting(User $user)
    {
        Hero::where('user_id', $user->id)->delete();
        $user->notifications()->delete();
        $user->tickets()->delete();

    }

    /**
     * Listen to the Entry saved event.
     *
     * @param Category $category
     * @return void
     */
    public function saved(User $user)
    {
        // Removing Entries from the Cache
        $this->clearCache($user);
    }

    /**
     * Listen to the Entry deleted event.
     *
     * @param Category $category
     * @return void
     */
    public function deleted(User $user)
    {
        // Removing Entries from the Cache
        $this->clearCache($user);
    }

    /**
     * Removing the Entity's Entries from the Cache
     *
     * @param $category
     */
    private function clearCache($user)
    {
        Cache::flush();
    }
}
