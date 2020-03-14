<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Attendance;
use App\Models\Coach;
use App\Models\Rate;
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
                // 'start_date' => "required|date-format:Y-m-d",
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
            $startDate = date("Y-m-d", strtotime(today()));
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
                'price' => $request->price,
            ]);
            $user->update(['subscribed' => 1]);
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

            /*    $validator = Validator::make($request->all(), [
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
                 }*/

            $subscriptions = $this->allMemberShip($user);
            if (count($subscriptions) > 0) {
                $total_count = $subscriptions->total();
                /*  $subscriptions->getCollection()->each(function ($subscription) {
                      unset($subscription['team']);
                      return $subscription;
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

    public function getAcademyMemberShip(Request $request)
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
                $subscriptions = $this->CurrentAcademyMemberShip($user);
                if ($subscriptions) {
                    $teamDays = $this->getTeamTimes($subscriptions->team_id);
                    $subscriptionsDays = getAllDateBetweenTwoDate($subscriptions->start_date, $subscriptions->end_date, $teamDays);
                    $userAttendanceDays = Attendance::where('user_id', $user->id)->where('subscription_id', $subscriptions->id)->pluck('attend', 'date')->toArray();
                    //if this date has been rated before by user "user rate the coach of his team"
                    $teamId = $subscriptions->team_id;
                    $coach = Coach::whereHas('teams', function ($q) use ($teamId) {
                        $q->where('id', $teamId);
                    })->select('id')->first();
                    $coachId = $coach->id;  // coach of user's team
                    //$this -> addUserAttendanceToEachDay($subscriptionsDays,$userAttendanceDays);
                    foreach ($subscriptionsDays as $day) {
                        if (array_key_exists($day->date, $userAttendanceDays))
                            $day->attend = (int)$userAttendanceDays[$day->date];
                        else
                            $day->attend = (int)0; //if not has attendance alway use be  absence
                        if ($this->checkIfDateRated($day->date, $coachId, $teamId, $user->id, 0)) //0 means  who make the rate is user
                            $day->rated = (int)1;
                        else
                            $day->rated = (int)0;
                    }

                    $subscriptions->attendances = $subscriptionsDays;
                    return $this->returnData('academySubscriptions', $subscriptions);
                } else {
                    return $this->returnError('E001', trans('messages.There are no data found'));
                }

            } else {
                $subscriptions = $this->PreviousAcademyMemberShip($user);
                $subscriptions->each(function ($subscription) {
                    unset($subscription->team->times);
                    return $subscription;
                });
                if (count($subscriptions) > 0) {
                    $total_count = $subscriptions->total();
                    $subscriptions = json_decode($subscriptions->toJson());
                    $subscriptionsJson = new \stdClass();
                    $subscriptionsJson->current_page = $subscriptions->current_page;
                    $subscriptionsJson->total_pages = $subscriptions->last_page;
                    $subscriptionsJson->total_count = $total_count;
                    $subscriptionsJson->data = $subscriptions->data;

                    return $this->returnData('academySubscriptions', $subscriptionsJson);
                }
                return $this->returnError('E001', trans('messages.There are no data found'));

            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getCurrentMemberShip(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "user_id" => 'required|exists:users,id',
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user = User::find($request->user_id);
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $subscriptions = $this->CurrentAcademyMemberShip($user);
            if ($subscriptions) {
                $teamDays = $this->getTeamTimes($subscriptions->team_id);
                $subscriptionsDays = getAllDateBetweenTwoDate($subscriptions->start_date, $subscriptions->end_date, $teamDays);
                $userAttendanceDays = Attendance::where('user_id', $user->id)->where('subscription_id', $subscriptions->id)->pluck('attend', 'date')->toArray();
                //if this date has been rated before by user "user rate the coach of his team"
                $teamId = $subscriptions->team_id;
                $coach = Coach::whereHas('teams', function ($q) use ($teamId) {
                    $q->where('id', $teamId);
                })->select('id')->first();
                $coachId = $coach->id;  // coach of user's team
                //$this -> addUserAttendanceToEachDay($subscriptionsDays,$userAttendanceDays);
                foreach ($subscriptionsDays as $day) {
                    if (array_key_exists($day->date, $userAttendanceDays))
                        $day->attend = (int)$userAttendanceDays[$day->date];
                    else
                        $day->attend = (int)0; //if not has attendance always use be  absence
                    if ($this->checkIfDateRated($day->date, $coachId, $teamId, $user->id, 1))   //1 means if who make the rate is coach
                        $day->rated = (int)1;
                    else
                        $day->rated = (int)0;
                }

                $subscriptions->attendances = $subscriptionsDays;
                $curren_app_subscriptions_fo_user = $user -> subscriptions -> where('status',1);
                $app_subscription = new \stdClass();
                $app_subscription -> id="";

                $subscriptions->app_subscription = $curren_app_subscriptions_fo_user? $curren_app_subscriptions_fo_user ->first()? : $app_subscription ;

                return $this->returnData('academySubscriptions', $subscriptions);
            } else {
                return $this->returnError('E001', trans('messages.There are no data found'));
            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

}
