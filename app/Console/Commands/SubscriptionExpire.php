<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use App\Models\User;
 use Illuminate\Console\Command;

class SubscriptionExpire extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:expire';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Expire all old subscription';


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
        $subscriptions = Subscription::where('status', 1)->whereHas('user')->get();
        foreach ($subscriptions as $subscription) {
             $subscription->update(['status' => 0]);
             User::where('id',$subscription -> user_id) -> update(['subscribed' => 0]);
        }
    }
}
