<?php

namespace App\Providers;

use App\Models\Replay;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Carbon::setWeekStartsAt(Carbon::SATURDAY);
        Carbon::setWeekEndsAt(Carbon::FRIDAY);
    }
}
