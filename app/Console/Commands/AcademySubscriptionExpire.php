<?php

namespace App\Console\Commands;

use App\Models\AcadSubscription;
use App\Models\Subscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class AcademySubscriptionExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academySubscription:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire all old academy subscription';


    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
         $subscriptions = AcadSubscription::where('status', 1)
            ->whereHas('user')
            ->whereDate('end_date', '<', date('Y-m-d'))
            ->get();
        foreach ($subscriptions as $subscription) {
            $subscription->update(['status' => 0]);
            User::whereDoesntHave('AcademySubscriptions', function ($query) {
                $query->where('status', 1);
            })
                ->where('id', $subscription->user_id)
                ->update(['academysubscribed' => 0]);
        }
    }
}
