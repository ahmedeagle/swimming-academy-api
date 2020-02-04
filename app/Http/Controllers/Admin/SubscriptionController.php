<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subscription;
use App\Models\User;
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
            $subscriptions = Subscription::expired()->get();
            $text = "الاشتراكات المنتهية";
        } /*elseif ($type == 'new') {
            $subscriptions = Subscription::where('end_date', '>=', today()->format('Y-m-d'))->where('status', 0)->get();
            $text = "الاشتراكات الجديده";
        }*/ else {
            $subscriptions = Subscription::get();
            $text = " جميع الاشتراكات";
        }
        return view('admin.subscriptions.index', compact('subscriptions'))->with('text', $text);
    }

    public function changeSubscriptionStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:0,1',
            'subscriptionId' => 'required|exists:subscriptions,id'
        ]);

        if ($validator->fails()) {
            return response()->json([], 422);
        }
        //expired subscription
        $subscription = Subscription::expired()->where('id', $request->subscriptionId)->first();
        if ($subscription) {
            return response()->json(['status' => 0, 'message' => 'لايمكن تغيير حاله اشتراك منتهي','id'=>  $request->subscriptionId], 200);
        }
        $subscription = Subscription::where('id', $request->subscriptionId)->first();
        $subscription->update(['status' => $request->status]);
        $subscription->user->update(['subscribed' => $request->status]);
        return response()->json(['status' => 1, 'message' => 'تم تغيير حاله الاشتراك بنجاح ','id'=>  $request->subscriptionId], 200);
    }
}
