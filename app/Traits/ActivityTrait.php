<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\Activity;
use App\Models\Event;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait ActivityTrait
{

    public function getAllActivities()
    {
        return Activity::active()->select('id', 'videoLink as link', DB::raw('title_' . $this->getCurrentLang() . ' as title'))->paginate(10);
    }

}
