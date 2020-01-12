<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Token;
use App\Models\User;
use App\Models\UserToken;
use App\Notifications\UserPasswordReset;
use App\Traits\GlobalTrait;
use App\Traits\SMSTrait;
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
    use UserTrait, GlobalTrait, SMSTrait;

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
                    "numeric",
                    "unique:users,mobile"
                ),
                "device_token" => "required|max:255",
                "password" => "required|confirmed||min:6|max:255",
                "agreement" => "required|boolean",
                "email" => "required|email|max:255|unique:users,email",
                "academy_id" => "required|exists:academies,id",
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
                    'academy_id' => $request->academy_id,
                    'team_id' => $request->team_id,
                    'tall' => $request->tall,
                    'weight' => $request->weight,
                    'birth_date' => $request->birth_date,
                    'api_token' => ''
                ]);

                $user->name = $user->getTranslatedName();
                $user->makeVisible(['status', 'name_en', 'name_ar']);
                DB::commit();
                return $this->returnData('user', json_decode(json_encode($this->authUserByMobile($request->mobile, $request->password), JSON_FORCE_OBJECT)));
            } catch (\Exception $ex) {
                DB::rollback();
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
            if ($user->status == 0) {
                if ($request->device_token != null) {
                    $user->device_token = $request->device_token;
                }
                $user->update();
                return $this->returnError('E331', __('messages.underRevision'));
            } elseif ($user->subscribed == 0) {
                if ($request->device_token != null) {
                    $user->device_token = $request->device_token;
                }
                $user->update();
                return $this->returnError('E331', __('messages.unsubscribe'));
            } else {
                DB::beginTransaction();
                if ($request->device_token != null) {
                    $user->device_token = $request->device_token;
                }
                $user->update();
                $user->name = $user->getTranslatedName();
                DB::commit();
            }
            return $this->returnData('user', json_decode(json_encode($user, JSON_FORCE_OBJECT)));
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
            return $this->returnData('user', json_decode(json_encode($user, JSON_FORCE_OBJECT)), ' تم التحقيق من الكود بنجاح ');
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
            return $this->returnData('user', json_decode(json_encode($user, JSON_FORCE_OBJECT)), trans('messages.confirm code send'));
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
                return $this->returnData('user', json_decode(json_encode($user, JSON_FORCE_OBJECT)), trans('messages.confirm code send'));
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


}
