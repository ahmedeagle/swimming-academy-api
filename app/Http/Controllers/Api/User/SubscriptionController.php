<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Coach;
use App\Models\Subscription;
use App\Models\Token;
use App\Models\User;
use App\Models\UserToken;
use App\Notifications\UserPasswordReset;
use App\Traits\GlobalTrait;
use App\Traits\SMSTrait;
use App\Traits\SubscriptionTrait;
use App\Traits\UserTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SubscriptionController extends Controller
{
    use UserTrait, GlobalTrait, SMSTrait, SubscriptionTrait;

    public function __construct(Request $request)
    {

    }

    //save user subscription
    public function paySubscription(Request $request)
    {
        try {
            $messages = [
                'mobile.required' => __('messages.mobileRequired'),
                'mobile.exists' => __('messages.mobileNotExists'),
                'start_date.required' => __('messages.startDateRequired'),
                'end_date.required' => __('messages.endDateRequired'),
                'start_date.date-format' => __('messages.startDateNotValidFormat'),
                'end_date.date-format' => __('messages.endDateNotValidFormat'),
            ];

            $rules = [
                "mobile" => array(
                    "required",
                    "regex:/^01[0-2]{1}[0-9]{8}/",
                    "exists:users,mobile",
                ),
                'start_date' => "required|date-format:Y-m-d",
                // 'end_date' => "required|date-format:Y-m-d",
                'price' => "required",

            ];

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            /* if ((date("m", strtotime($request->start_date)) != date("m")) || (date("m", strtotime($request->end_date)) != date("m"))) {
                 return $this->returnError('E001', __('messages.must pay only for current month'));
             }*/

            $daystosum = 29;
            $startDate = date("Y-m-d", strtotime($request->start_date));
            $endDate = date("Y-m-d", strtotime($request->start_date . ' + ' . $daystosum . ' days'));

            $user = User::where('mobile', $request->mobile)->first();
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }
            if ($user->subscribed == 1)
                return $this->returnError('E001', trans('messages.already Subscribed this month'));

            Subscription::create([
                'user_id' => $user->id,
                'team_id' => $user->team_id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'price'   => $request -> price,
            ]);
            $user->update(['subscribed' => 1, 'status' => 1]);
            return $this->returnSuccessMessage('S001', trans('messages.userSubscribedSucessfullyAndWaitForAdminApproved'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
    
    public function checkSubscribtion(Request $request)
    {
        $messages = [
            'mobile.required' => __('messages.mobileRequired'),
            'mobile.exists' => __('messages.mobileNotExists'),
        ];
        $validator = Validator::make($request->all(), [
            "mobile" => array(
                "required",
                "regex:/^01[0-2]{1}[0-9]{8}/",
                "exists:users,mobile"
            ),
        ], $messages);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        $user = User::where('mobile', $request->mobile)->first();
        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }
        if ($user->subscribed == 1)
            return $this->returnSuccessMessage('S001', trans('messages.already Subscribed this month'));
        else
            return $this->returnSuccessMessage('S001', trans('messages.user not Subscribed'));
    }

    public function getMemberShip(Request $request)
    {
        try {
            $user = $this->auth('user-api');
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }

            $validator = Validator::make($request->all(), [
                "type" => "required|in:current,previous",
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            if ($request->type == 'current') {
                $subscriptions = $this->CurrentMemberShip($user);
            } else {
                $subscriptions = $this->PreviousMemberShip($user);
            }

            if (count($subscriptions) > 0) {
                $total_count = $subscriptions->total();
                /* $subscriptions->getCollection()->each(function ($activity) {
                     return $activity;
                 });*/
                $subscriptions = json_decode($subscriptions->toJson());
                $subscriptionsJson = new \stdClass();
                $subscriptionsJson->current_page = $subscriptions->current_page;
                $subscriptionsJson->total_pages = $subscriptions->last_page;
                $subscriptionsJson->total_count = $total_count;
                $subscriptionsJson->data = $subscriptions->data;
                return $this->returnData('subscriptions', $subscriptionsJson);
            }

            return $this->returnError('E001', trans('messages.There are no data found'));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }
}
