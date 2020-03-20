<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\AcadSubscription;
use App\Models\Coach;
use App\Models\Notification;
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

class UserController extends Controller
{
    use UserTrait, GlobalTrait, SMSTrait, SubscriptionTrait;

    public function __construct(Request $request)
    {

    }


    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "name_ar" => "required|max:255",
                "name_en" => "required|max:255",
                "address_ar" => "sometimes|nullable|max:255",
                "address_en" => "sometimes|nullable|max:255",
                "mobile" => array(
                    "required",
                    "string",
                    "max:11",
                    "unique:users,mobile",
                    "regex:/^01[0-2]{1}[0-9]{8}/",
                ),
                "device_token" => "required|max:255",
                "password" => "required||min:6|max:255",
                "agreement" => "required|boolean",
                "email" => "required|email|max:255|unique:users,email",
                "academy_code" => "required|exists:academies,code",
                "category_id" => "required|exists:categories,id",
                "team_id" => "required|exists:teams,id",
                "photo" => "required",
                "birth_date" => "required|date-format:Y-m-d",
                "tall" => "sometimes|nullable|max:100",
                "weight" => "sometimes|nullable|max:100",
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            if (!$request->agreement)
                return $this->returnError('E006', trans('messages.Agreement is required'));

            DB::beginTransaction();
            try {

                if (isset($request->photo) && !empty($request->photo)) {
                    $fileName = $this->saveImage('users', $request->photo);
                }

                $academy_id = Academy::where('code', $request->academy_code)->value('id');

                $user = User::create([
                    'name_en' => trim($request->name_en),
                    'name_ar' => trim($request->name_ar),
                    'address_ar' => trim($request->address_ar),
                    'address_en' => trim($request->address_en),
                    'password' => $request->password,
                    'mobile' => $request->mobile,
                    'photo' => $fileName,
                    'status' => 0,
                    'device_token' => $request->device_token,
                    'email' => $request->email,
                    'academy_id' => $academy_id,
                    'team_id' => $request->team_id,
                    'category_id' => $request->category_id,
                    'tall' => $request->tall,
                    'weight' => $request->weight,
                    'birth_date' => $request->birth_date,
                    'api_token' => ''
                ]);

                $user->name = $user->getTranslatedName();
                $user->makeVisible(['status', 'name_en', 'name_ar']);

                $content_ar = __('messages.new player registration') . ' ' . $user->name_ar . ' ' . __('messages.in category') . ' ' . $user->category->name_ar . ' ' . __('messages.in team') . ' ' . $user->team->name_ar;
                // only admin how can see the coaches rates
                $notification = Notification::create([
                    'title_ar' => 'تسجيل لاعب جديد',
                    'title_en' => 'تسجيل لاعب جديد',
                    'content_ar' => $content_ar,
                    'content_en' => $content_ar,
                    'notificationable_type' => 'App\Models\User',
                    'notificationable_id' => $user->id,
                    'type' => 1 //new user  registration
                ]);

                $notify = [
                    'user_name' => $user->name_ar,
                    'content' => $content_ar,
                    'notification_id' => $notification->id,
                    'photo' => $user->photo
                ];

                DB::commit();

                //fire pusher  notification for admin
                event(new \App\Events\NewRegisteration($notify));   // fire pusher message event notification*/

                return $this->returnData('user', json_decode(json_encode($this->authUserByMobile($request->mobile, $request->password))), __('messages.registered succussfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public
    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "device_token" => "required|max:255",
            "mobile" => "required",
            "password" => "required",
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        $user = $this->authUserByMobile($request->mobile, $request->password);

        if ($user != null) {

            if ($user->subscribed == 0) {
                return $this->returnError('E338', __('messages.unsubscribe'));
            } else {
                DB::beginTransaction();
                $user->device_token = $request->device_token;
                $user->update();
                $user->name = $user->getTranslatedName();
                DB::commit();
            }
            return $this->returnData('user', json_decode(json_encode($user)));
        }
        return $this->returnError('E001', trans('messages.No result, please check your registration before'));
    }

    public function CodeVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "api_token" => "required",
                "activation_code" => "required"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user = $this->getUserByTempToken($request->api_token);
            if (!$user) {
                return $this->returnError('E001', trans('messages.no user found'));
            }

            if ($user->activation_code != $request->activation_code)
                return $this->returnError('E001', trans('messages.This code is not valid please enter it again'));
            $user->update(['activation_code' => '']);
            $user->name = $user->getTranslatedName();
            $user->makeVisible(['api_token', 'status', 'name_en', 'name_ar']);
            return $this->returnData('user', json_decode(json_encode($user)), ' تم التحقيق من الكود بنجاح ');
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function resendCodeVerification(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "api_token" => "required",
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user = $this->getUserByTempToken($request->api_token);
            if (!$user) {
                return $this->returnError('E001', trans('messages.no user found'));
            }
            $code = $this->getRandomString(4);
            $user->update(['activation_code' => $code]);
            $user->notify(new UserPasswordReset($code));
            return $this->returnData('user', json_decode(json_encode($user)), trans('messages.confirm code send'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "mobile" => array(
                    "required",
                    "exists:users,mobile"
                )
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $user = $this->getUserMobileOrEmail($request->mobile);
            if (!$user) {
                return $this->returnError('E001', trans('messages.No user with this mobile'));
            }
            DB::beginTransaction();
            try {
                $code = $this->getRandomString(4);
                $tempToken = $this->getRandomString(250);
                $user->update(['api_token' => $tempToken, 'activation_code' => $code]);
                $user->notify(new UserPasswordReset($code));
                return $this->returnData('user', json_decode(json_encode($user)), trans('messages.confirm code send'));
            } catch (\Exception $ex) {
                DB::rollback();
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }


    public function passwordReset(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "password" => "required|confirmed|max:255|min:6",
                "api_token" => "required"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $user = $this->getUserByTempToken($request->api_token);
            if (!$user) {
                return $this->returnError('E001', trans('messages.no user found'));
            }

            DB::beginTransaction();

            try {
                $user->update([
                    'password' => $request->password,
                    'activation_code' => ''
                ]);
                DB::commit();
                return $this->returnSuccessMessage(trans('messages.password reset Successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function logout(Request $request)
    {
        try {
            $user = $this->auth('user-api');
            $token = $request->api_token;
            UserToken::where('api_token', $token)->delete();
            $user->api_token = '';
            $user->update();
            return $this->returnData('message', trans('messages.Logged out successfully'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public function update_user_profile(Request $request)
    {
        $user = $this->auth('user-api');
        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        try {

            $rules = [
                "email" => "required|email|max:255|unique:users,email," . $user->id,
            ];

            if ($request->has('password')) {
                $rules['password'] = "required|nullable|confirmed||min:6|max:255";
                $rules['old_password'] = "required";
            }

            if ($request->has('photo')) {
                $rules['photo'] = "required";
            }

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $fileName = $user->photo;
            if (isset($request->photo) && !empty($request->photo)) {
                $fileName = $this->saveImage('users', $request->photo);
            }

            if ($request->password) {
                //check for old password
                if (Hash::check($request->old_password, $user->password)) {
                    $user->update([
                        'password' => $request->password,
                    ]);
                } else {
                    return $this->returnError('E002', trans('messages.invalid old password'));
                }
            }

            $user->update(['photo' => $fileName] + $request->except('photo'));
            $user = $this->getAllData($user->id);
            $user->name = $user->{'name_' . app()->getLocale()};
            return $this->returnData('user', json_decode(json_encode($user)),
                trans('messages.User data updated successfully'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

    public function myTeam(Request $request)
    {
        try {
            $user = $this->auth('user-api');
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }
            $userData = User::with(['academy' => function ($q) {
                $q->select('id', DB::raw('name_' . app()->getLocale() . ' as name'), 'code', 'logo');
            }, 'team' => function ($q) {
                $q->select('id', 'coach_id', DB::raw('name_' . app()->getLocale() . ' as name'), DB::raw('level_' . app()->getLocale() . ' as level'), 'photo');
                $q->with(['coach' => function ($qq) {
                    $qq->select('id', 'name_' . app()->getLocale() . ' as name', 'photo');
                }]);
            }, 'category' => function ($qq) {
                $qq->select('id', 'name_' . app()->getLocale() . ' as name');
            }, 'AcademySubscriptions' => function ($qq) {
                $qq->where('status', 1)->first();
            }])->select('id', 'team_id', 'academy_id', 'category_id', 'name_' . app()->getLocale() . ' as name', 'photo')->find($user->id);

            if ($userData) {
                return $this->returnData('user', $userData);
            } else
                return $this->returnError('E001', trans('messages.There are no activities found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    function getRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $chkCode = User::where('activation_code', $string)->first();
        if ($chkCode) {
            $this->getRandomString(6);
        }
        return $string;
    }

    public function rateCoach(Request $request)
    {
        try {

            $messages = [
                "rate.required" => __('messages.rate is required'),
            ];
            $validator = Validator::make($request->all(), [
                "rate" => "required|in:1,2,3,4,5",
                "subscription_id" => "required|exists:academysubscriptions,id",
                "day_name" => "required|max:100",
                "date" => "required|date-format:Y-m-d",
            ], $messages);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            if ($request->rate != 5) {
                if (!$request->filled('comment'))
                    return $this->returnError('E001', trans('messages.comment is required'));
            }


            $user = $this->auth('user-api');
            if (!$user) {
                return $this->returnError('E001', trans('messages.no user found'));
            }

            $ratedBefore = Rate::where([
                ['user_id', $user->id],
                ['coach_id', $user->team->coach->id],
                ['team_id', $user->team->id],
                ['subscription_id', $request->subscription_id],
                ['date', $request->date],
                ['rateable', 0],
            ])->first();

            if ($ratedBefore) {
                return $this->returnError('E001', trans('messages.rated before'));
            }

            DB::beginTransaction();
            try {
                Rate::create([
                    'rate' => $request->rate,
                    'comment' => $request->comment,
                    'user_id' => $user->id,
                    'coach_id' => $user->team->coach->id,
                    'team_id' => $user->team->id,
                    'rateable' => 0, //user who rate "user rate coach"
                    'subscription_id' => $request->subscription_id,
                    'day_name' => strtolower($request->day_name),
                    'date' => $request->date,
                ]);

                $content_ar = __('messages.the player') . ' ' . $user->name_ar . ' ' . __('messages.rate the coach') . ' ' . $user->team->coach->name_ar . ' ' . $request->rate . ' ' . __('messages.comment') . ' ' . $request->comment;
                // only admin how can see the coaches rates
                $notification = Notification::create([
                    'title_ar' => __('messages.the player') . ' ' . $user->name_ar . ' ' . __('messages.rate the coach') . ' ' . $user->team->coach->name_ar . ' ' . __('messages.with rate') . ':' . $request->rate,
                    'title_en' => __('messages.the player') . ' ' . $user->name_ar . ' ' . __('messages.rate the coach') . ' ' . $user->team->coach->name_ar . ' ' . __('messages.with rate') . $request->rate,
                    'content_ar' => $content_ar,
                    'content_en' => __('messages.the player') . ' ' . $user->name_ar . ' ' . __('messages.rate the coach') . ' ' . $user->team->coach->name_ar . ' ' . $request->rate . ' ' . __('messages.comment') . ' ' . $request->comment,
                    'notificationable_type' => 'App\Models\User',
                    'notificationable_id' => $user->id,
                    'type' => 2 // rate coach notifications
                ]);

                $notify = [
                    'coach_name' => $user->team->coach->name_ar,
                    'user_name' => $user->team->name_ar,
                    'content' => $content_ar,
                    'notification_id' => $notification->id,
                    'photo' => $user->photo,
                ];
                //fire pusher  notification for admin
                event(new \App\Events\NewNotification($notify));   // fire pusher message event notification
                DB::commit();
                return $this->returnSuccessMessage(trans('messages.rate sent successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public function getRates(Request $request)
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
                $currentSubscription = AcadSubscription::where('status', 1)->where('user_id', $user->id)->first();
                $rates = $this->currentRates($currentSubscription->id);
            } else {
                $previousSubscriptionsIds = Subscription::expired()->where('user_id', $user->id)->pluck('id')->toArray();
                $rates = $this->previousRates($previousSubscriptionsIds);
            }

            if (count($rates) > 0) {
                $total_count = $rates->total();
                $rates = json_decode($rates->toJson());
                $ratesJson = new \stdClass();
                $ratesJson->current_page = $rates->current_page;
                $ratesJson->total_pages = $rates->last_page;
                $ratesJson->total_count = $total_count;
                $ratesJson->data = $rates->data;
                return $this->returnData('rates', $ratesJson);
            }

            return $this->returnError('E001', trans('messages.There are no data found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

}
