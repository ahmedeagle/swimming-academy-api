<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Validator;
use Hash;

class SubscriptionController extends Controller
{
    public function subscriptions(Request $request)
    {
        $type = $request->type;
        if ($type) {
            if ($type != 'all' && $type != 'expired' && $type != 'current' && $type != 'new') {
                return redirect()->back();
            }
        } else {
            return redirect()->back();
        }

        $text = "";
        if ($type == 'current') {
            $subscriptions = Subscription::where('status', 1)->get();
            $text = "الاشتراكات الحالية";
        } elseif ($type == 'expired') {
            $subscriptions = Subscription::where('end_date', '<', today()->format('Y-m-d'))->where('status', 0)->get();
            $text = "الاشتراكات المنتهية";
        } elseif ($type == 'new') {
            $subscriptions = Subscription::where('end_date', '>=', today()->format('Y-m-d'))->where('status', 0)->get();
            $text = "الاشتراكات الجديده";
        } else {
            $subscriptions = Subscription::get();
            $text = "الاشتراكات المنتهية";
        }
        return view('admin.subscriptions.index', compact('subscriptions')) -> with('text',$text);
    }
}
