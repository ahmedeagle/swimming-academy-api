<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\AcadSubscription;
use App\Models\Category;
use App\Models\Notification;
use App\Models\Rate;
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

    ////////////////application subscription//////////////////

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
            return response()->json(['status' => 0, 'message' => 'لايمكن تغيير حاله اشتراك منتهي', 'id' => $request->subscriptionId], 200);
        }
        $subscription = Subscription::where('id', $request->subscriptionId)->first();
        $subscription->update(['status' => $request->status]);
        $subscription->user->update(['subscribed' => $request->status]);
        return response()->json(['status' => 1, 'message' => 'تم تغيير حاله الاشتراك بنجاح ', 'id' => $request->subscriptionId], 200);
    }


    public function addCashSubscription()
    {

        $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();

        $data['categories'] = Category::active()
            ->select('categories.id', 'categories.name_' . app()->getLocale() . ' as name')
            ->whereHas('allUsers', function ($qq) {
                $qq->active()->notSubScribed();
            })->get();

        return view('admin.subscriptions.create', $data);
    }


    public function storeCashSubscription(Request $request)
    {

        try {
            $messages = [
                'price.required' => 'رجاء ادخال قيمة الاشتراك',
            ];

            $rules = [
                'price' => "required|numeric|min:0",
                'userId' => "required"
            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with(['subscriptionModalId' => $request->userId]);
            }

            $daystosum = 29;
            $startDate = date("Y-m-d", strtotime(today()));
            $endDate = date("Y-m-d", strtotime($request->start_date . ' + ' . $daystosum . ' days'));

            $user = User::find($request->userId);
            if (!$user) {
                notify()->error('ألمستخدم غير موجود لدينا');
                return redirect()->back()->withErrors(['price' => 'ألمستخدم غير مسجل لدينا'])->withInput($request->all())->with(['subscriptionModalId' => $request->userId]);
            }

            if ($user->subscribed == 1) {
                notify()->error('يوجد أشتراك حالي لهذا الاعب');
                return redirect()->back()->withErrors(['price' => 'يوجد أشتراك حالي لهذا الاعب'])->withInput($request->all())->with(['subscriptionModalId' => $request->userId]);
            }

            Subscription::create([
                'user_id' => $user->id,
                'team_id' => $user->team_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price' => $request->price,
            ]);
            $user->update(['subscribed' => 1]);


            //send notification
            $notification = Notification::create([
                'title_ar' => __('messages.application subscription'),
                'title_en' => __('messages.application subscription'),
                'content_ar' => __('messages.application subscription is activated'),
                'content_en' => __('messages.application subscription is activated'),
                'notificationable_type' => 'App\Models\User',
                'notificationable_id' => $user->id,
                'type' => 6 //  application  subscription
            ]);

            //send push notification to user
            (new \App\Http\Controllers\PushNotificationController(['title' => __('messages.application subscription'), 'body' => __('messages.application subscription is activated')]))->send($user->device_token);

            notify()->success('تم تفعيل الاشتراك بنجاح');
            return redirect()->route('admin.users.all');
        } catch (\Exception $ex) {
            return $ex;
        }
    }

    ////////////////////academy subscription ////////////////////////

    public function academySubscriptions(Request $request)
    {
        $type = $request->type;

        if ($type) {
            if ($type != 'all' && $type != 'expired' && $type != 'current' && $type != 'new') {
                notify()->error('النوع غير موجود لدينا ');
                return redirect()->route('admin.users.all');
            }
        } else {
            notify()->error('النوع مطلوب');
            return redirect()->route('admin.users.all');
        }

        if (!$request->filled('user_id')) {
            notify()->error('المستخدم مطلوب');
            return redirect()->route('admin.users.all');
        }

        $user = User::find($request->user_id);
        if (!$user) {
            notify()->error('المستخدم غير موجود لدينا  ');
            return redirect()->route('admin.users.all');
        }
        $text = "";
        if ($type == 'current') {
            $subscriptions = AcadSubscription::where('status', 1)->where('user_id', $user->id)->orderBy('end_date', 'DESC')->get();
            $text = "   الاشتراكات الحالية للاعب -  " . $user->name_ar;
        } elseif ($type == 'expired') {
            $subscriptions = AcadSubscription::expired()->where('user_id', $user->id)->orderBy('end_date', 'DESC')->get();
            $text = " الاشتراكات المنتهية للاعب - " . $user->name_ar;
        } /*elseif ($type == 'new') {
            $subscriptions = Subscription::where('end_date', '>=', today()->format('Y-m-d'))->where('status', 0)->get();
            $text = "الاشتراكات الجديده";
        }*/ else {
            $subscriptions = AcadSubscription::where('user_id', $user->id)->orderBy('end_date', 'DESC')->get();
            $text = "   جميع الاشتراكات للاعب - " . $user->name_ar;
        }
        return view('admin.academies.subscriptions.index', compact('subscriptions'))->with(['text' => $text, 'userId' => $user->id]);
    }

    public function createAcademySubscriptions($userId)
    {
        $user = User::find($userId);
        if (!$user) {
            notify()->error('المستخدم غير موجود لدينا');
            return redirect()->route('admin.users.all');
        }

        return view('admin.academies.subscriptions.create', compact('user'));
    }

    public function storeAcademySubscriptions(Request $request)
    {
        try {
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'price.numeric' => 'السعر غير صحيح',
                'price.min' => 'السعر غير صحيح',
                'date-format' => 'صيغة التاريخ غير مقبولة ',
            ];
            $validator = Validator::make($request->all(), [
                'price' => 'required|numeric|min:0',
                'start_date' => "required|date-format:Y-m-d",
                'end_date' => 'required|date-format:Y-m-d',
                'user_id' => 'required|exists:users,id',
            ], $messages);
            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $user = User::find($request->user_id);

            if (date('Y-m-d', strtotime($request->end_date)) <= date('Y-m-d')) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors(['end_date' => 'لابد ان يكون تاريخ انتهاء الاشتراك اكبر من تاريخ اليوم'])->withInput($request->all());
            }

            if (date('Y-m-d', strtotime($request->end_date)) <= date('Y-m-d', strtotime($request->start_date))) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors(['end_date' => 'لابد ان يكون تاريخ انتهاء الاشتراك اكبر من تاريخ بداية الاشتراك'])->withInput($request->all());
            }

            $thereAreActiveSubscription = AcadSubscription::where('user_id', $user->id)->where('team_id', $user->team_id)->where('status', 1)->first();

            if ($thereAreActiveSubscription) {
                notify()->error(' عفوا هناك اشتراك حالي لهذا المستخدم ');
                return redirect()->route('admin.users.all');
            }
            AcadSubscription::create([
                'user_id' => $user->id,
                'team_id' => $user->team_id,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'price' => $request->price,
            ]);

            $today = date('Y-m-d');

            // if($today >= date('Y-m-d', strtotime($request->start_date)) && $today <= date('Y-m-d', strtotime($request->end_date))){
            User::where('id', $user->id)->update(['status' => 1, 'academysubscribed' => 1]);
            //}


            //send notification
            $notification = Notification::create([
                'title_ar' => __('messages.academy subscription'),
                'title_en' => __('messages.academy subscription'),
                'content_ar' => __('messages.academy subscription is activated'),
                'content_en' => __('messages.academy subscription is activated'),
                'notificationable_type' => 'App\Models\User',
                'notificationable_id' => $user->id,
                'type' => 5 //  academy  subscription
            ]);

            //send push notification to user
            (new \App\Http\Controllers\PushNotificationController(['title' => __('messages.academy subscription'), 'body' => __('messages.academy subscription is activated')]))->send($user->device_token);


            notify()->success('تم اضافه الاشتراك بنجاح ');
            return redirect()->route('admin.users.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


}
