<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Notification;
use App\Models\Rate;
use App\Models\Token;
use App\Models\User;
use App\Traits\GlobalTrait;
use App\Traits\SMSTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CoachTrait;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class CoachController extends Controller
{
    use CoachTrait, GlobalTrait, SMSTrait;

    public function __construct(Request $request)
    {

    }


    public
    function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "device_token" => "required|max:255",
            "mobile" => "required",
            "password" => "required|string|min:6",
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        $coach = $this->authCoachByMobile($request->mobile, $request->password);

        if ($coach != null) {
            if ($coach->status == '0' or $coach->status == 0) {
                if ($request->device_token != null) {
                    $coach->device_token = $request->device_token;
                }
                $coach->update();
                return $this->returnError('E332', __('messages.underRevision'));
            } else {
                DB::beginTransaction();
                if ($request->device_token != null) {
                    $coach->device_token = $request->device_token;
                }
                $coach->update();
                $coach->name = $coach->getTranslatedName();
                // $coach->makeHidden(['name_ar', 'name_en']);
                DB::commit();
            }
            $coach->makeVisible(['activation_code', 'status']);
            return $this->returnData('coach', json_decode(json_encode($coach, JSON_FORCE_OBJECT)));
        }
        return $this->returnError('E001', trans('messages.No result, please check your registration before'));
    }

    public function teams(Request $request)
    {
        try {
            $coach = $this->auth('coach-api');
            $teams = $this->getTeams($coach, $request);
            if (count($teams->toArray()) > 0) {
                $total_count = $teams->total();
                $teams->getCollection()->each(function ($team) {
                    $team->name = $team->getTranslatedName();
                    $team->level = $team->getTranslatedLevel();
                    $team->makeHidden(['pivot', 'academy_id', 'name_ar', 'name_en', 'level_ar', 'level_en', 'category_id', 'coach_id']);
                    return $team;
                });
                $branches = json_decode($teams->toJson());
                $branchesJson = new \stdClass();
                $branchesJson->current_page = $branches->current_page;
                $branchesJson->total_pages = $branches->last_page;
                $branchesJson->total_count = $total_count;
                $branchesJson->data = $branches->data;
                return $this->returnData('teams', $branchesJson);
            }
            return $this->returnError('E001', trans('messages.There are no teams found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function prepare_update_coach_profile(Request $request)
    {
        try {
            $coach = $this->auth('coach-api');
            if (!$coach) {
                return $this->returnError('D000', trans('messages.User not found'));
            }

            $coach = $this->getAllData($coach->id);
            $coach->name = $coach->{'name_' . app()->getLocale()};

            return $this->returnData('coach', json_decode(json_encode($coach, JSON_FORCE_OBJECT)));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function update_coach_profile(Request $request)
    {
        $coach = $this->auth('coach-api');

        if (!$coach) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        $validator = Validator::make($request->all(), [
            "password" => "sometimes|nullable|confirmed|max:255",
            "password_confirmation" => "required_with:password",
            "old_password" => "required_with:password",
            "photo" => "sometimes|nullable",
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        DB::beginTransaction();

        $fileName = $coach->photo;
        if (isset($request->photo) && !empty($request->photo)) {
            $fileName = $this->saveImage('coaches', $request->photo);
        }

        if ($request->password) {
            //check for old password
            if (Hash::check($request->old_password, $coach->password)) {
                $coach->update([
                    'password' => $request->password,
                    'photo' => $fileName
                ]);
            } else {
                return $this->returnError('E002', trans('messages.invalid old password'));
            }

        } else {
            $coach->update([
                'photo' => $fileName
            ]);
        }
        //  $coach->makeVisible(['status']);
        $coach = $this->getAllData($coach->id);
        $coach->name = $coach->{'name_' . app()->getLocale()};
        DB::commit();
        return $this->returnData('coach', json_decode(json_encode($coach, JSON_FORCE_OBJECT)),
            trans('messages.coach data updated successfully'));
    }


    public
    function logout(Request $request)
    {
        try {
            $coach = $this->auth('coach-api');
            $token = $request->api_token;
            Token::where('api_token', $token)->delete();
            $coach->api_token = '';
            $coach->update();
            return $this->returnData('message', trans('messages.Logged out successfully'));

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


    public function rateUser(Request $request)
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
                "user_id" => "required|exists:users,id"
            ], $messages);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            if ($request->rate != 5) {
                if (!$request->filled('comment'))
                    return $this->returnError('E001', trans('messages.comment is required'));
            }


            $coach = $this->auth('coach-api');
            $user = User::find($request->user_id);
            if (!$coach or !$user) {
                return $this->returnError('E001', trans('messages.no user found'));
            }


            $ratedBefore = Rate::where([
                ['user_id', $user->id],
                ['coach_id', $coach->id],
                ['team_id', $user->team->id],
                ['subscription_id', $request->subscription_id],
                ['date', $request->date],
                ['rateable', 1],
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
                    'coach_id' => $coach->id,
                    'team_id' => $user->team->id,
                    'rateable' => 1, //user who rate "user rate coach"
                    'subscription_id' => $request->subscription_id,
                    'day_name' => strtolower($request->day_name),
                    'date' => $request->date,
                ]);

                $content = __('messages.the coach') . ' ' . $coach->name_ar . ' ' . __('messages.rate the user') . ' ' . $user->name_ar . ' ' . $request->rate . ' ' . __('messages.stars') . ' ' . __('messages.comment') . ' ' . $request->comment;

                //send notification
                $notification = Notification::create([
                    'title_ar' => __('messages.rate for user') . ' ' . $user->name_ar,
                    'title_en' => __('messages.rate for user') . ' ' . $user->name_ar,
                    'content_ar' => __('messages.the coach') . ' ' . $coach->name_ar . ' ' . __('messages.rate the user') . ' ' . $user->name_ar . ' ' . $request->rate . ' ' . __('messages.stars') . ' ' . __('messages.comment') . ' ' . $request->comment,
                    'content_en' => __('messages.the coach') . ' ' . $coach->name_ar . ' ' . __('messages.rate the user') . ' ' . $user->name_ar . ' ' . $request->rate . ' ' . __('messages.stars') . ' ' . __('messages.comment') . ' ' . $request->comment,
                    'notificationable_type' => 'App\Models\User',
                    'notificationable_id' => $user->id,
                    'type' => 3 //  coach rate user
                ]);

                $content = __('messages.the coach') . ' ' . $coach->name_ar . ' ' . __('messages.rate the user') . ' ' . $user->name_ar . ' ' . $request->rate . ' ' .__('messages.stars').' '. __('messages.comment') . ' ' . $request->comment;
                $notify = [
                    'user_name' => $user->name_ar,
                    'content' => $content,
                    'notification_id' => $notification->id,
                    'user_id' => $user->id,
                    'photo' => $user->photo,
                ];
                //fire pusher  notification for admin
                event(new \App\Events\NewCoachRateNotification($notify));   // fire pusher message event notification

                DB::commit();
                //send push notification to user
                (new \App\Http\Controllers\PushNotificationController(['title' => __('messages.new rating'), 'body' => $content]))->send($user->device_token);

                return $this->returnSuccessMessage(trans('messages.rate sent successfully'));
            } catch (\Exception $ex) {
                DB::rollback();
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

}
