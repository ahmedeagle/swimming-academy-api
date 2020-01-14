<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\Event;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait EventTrait
{

    public function getAllEvents()
    {
        return Event::active()->select('id', 'photo', DB::raw('title_' . $this->getCurrentLang() . ' as title'), DB::raw('description_' . $this->getCurrentLang() . ' as description'))->paginate(10);
    }

}
