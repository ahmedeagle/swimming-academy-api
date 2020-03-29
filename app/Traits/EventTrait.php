<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\Coach;
use App\Models\Event;
use App\Models\User;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait EventTrait
{

    public function getAllEvents(User $user)
    {
        return Event::with('images')->active()->where('category_id',$user -> category -> id)->select('id', 'photo', DB::raw('title_' . $this->getCurrentLang() . ' as title'), DB::raw('description_' . $this->getCurrentLang() . ' as description'))->paginate(10);
    }

    public function getAllCoachesEvents(Coach $coach)
    {
        return Event::with('images')->active()->where('category_id',$coach -> category -> id)->select('id', 'photo', DB::raw('title_' . $this->getCurrentLang() . ' as title'), DB::raw('description_' . $this->getCurrentLang() . ' as description'))->paginate(10);
    }
}
