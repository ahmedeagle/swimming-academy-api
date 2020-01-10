<?php

namespace App\Http\Controllers\Api\Coach;

use App\Http\Controllers\Controller;
use App\Models\Coach;
use App\Models\Token;
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
            "password" => "required|max:255",
            "mobile" => "required",
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        $coach = $this->authCoachByMobile($request->mobile, $request->password);

        if ($coach != null) {
            if (($coach->status == '0' or $coach->status == 0)) {
                if ($request->device_token != null) {
                    $coach->device_token = $request->device_token;
                }
                $coach->update();
                return $this->returnError('E001', __('messages.underRevision'));
            } else {
                DB::beginTransaction();
                if ($request->device_token != null) {
                    $coach->device_token = $request->device_token;
                }
                $coach->update();
                DB::commit();
            }
            $coach->makeVisible(['activation_code', 'status']);
            return $this->returnData('coach', json_decode(json_encode($coach, JSON_FORCE_OBJECT)));
        }
        return $this->returnError('E001', trans('messages.No result, please check your registration before'));
    }


    /////

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "rate" => "boolean",   // provider rate
            "type_id" => "array",   // provider type clinic - doctor - hospital  - ....
            "queryStr" => "sometimes|nullable"
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $user = null;
        if ($request->api_token)
            $user = User::where('api_token', $request->api_token)->first();

        $order = (isset($request->order) && strtolower($request->order) == "desc") ? "DESC" : "ASC";
        $validation = $this->checkValidationFields('', '', '', $request->type_id);
        if (is_array($request->type_id) && count($request->type_id) > 0) {
            if (count($request->type_id) != $validation->type_found)
                return $this->returnError('D000', trans('messages.There is no type with this id'));
        }


        // deprecated
        $queryStr = "";
        if ($request->filled('queryStr')) {
            $queryStr = $request->queryStr;
        }

        // first if statment depricated because fillter remove from here
        if (isset($request->nearest_date) && $request->nearest_date != 0) {
            $nearest_date = $request->nearest_date;
            if (isset($request->specification_id) && $request->specification_id != 0) {
                $specification_id = $request->specification_id;
                $providers = $this->getSortedByDoctorDates(($user != null ? $user->id : null), ($user != null && strlen($user->longitude) > 6 ? $user->longitude : $request->longitude), ($user != null && strlen($user->latitude) > 6 ? $user->latitude : $request->latitude),
                    $order, $request->rate, $request->type_id, $nearest_date, $specification_id);

                $providers1 = $providers[0];
                if (count($providers1->toArray()) > 0) {
                    $total_count = $providers1->total();
                    $providers1 = json_decode($providers1->toJson());
                    $providersJson = new \stdClass();
                    $providersJson->current_page = $providers1->current_page;
                    $providersJson->total_pages = $providers1->last_page;
                    $providersJson->total_count = $total_count;
                    $providersJson->data = $providers[1];

                    if (!empty($providersJson->data) && count($providersJson->data) > 0) {
                        $providersJson = $this->addProviderNameToresults($providersJson);
                    }

                    return $this->returnData('providers', $providersJson); //  nearst date
                }
            } else
                return $this->returnError('E001', trans('messages.you must choose doctor specification'));
        } else {
            $providers = $this->getProvidersBranch(($user != null ? $user->id : null), ($user != null && strlen($user->longitude) > 6 ? $user->longitude : $request->longitude), ($user != null && strlen($user->latitude) > 6 ? $user->latitude : $request->latitude),
                $order, $request->rate, $request->type_id, 0, 0, $queryStr);
            if (count($providers->toArray()) > 0) {
                $total_count = $providers->total();
                $providers->getCollection()->each(function ($provider) {
                    $provider->favourite = count($provider->favourites) > 0 ? 1 : 0;
                    $provider->distance = (string)number_format($provider->distance * 1.609344, 2);
                    unset($provider->favourites);
                    return $provider;
                });

                $providers = json_decode($providers->toJson());
                $providersJson = new \stdClass();
                $providersJson->current_page = $providers->current_page;
                $providersJson->total_pages = $providers->last_page;
                $providersJson->total_count = $total_count;
                $providersJson->data = $providers->data;

                if (!empty($providersJson->data) && count($providersJson->data) > 0) {
                    $providersJson = $this->addProviderNameToresults($providersJson);
                }
                return $this->returnData('providers', $providersJson);
            }

        }
        return $this->returnError('E001', trans('messages.No data founded'));

    }

    public function forgetPassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "mobile" => array(
                    "required",
                    "numeric",
                    "digits_between:8,10",
                    "regex:/^(009665|9665|\+9665|05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/",
                    "exists:providers,mobile"
                ),
                "type" => "required|in:0,1"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $provider = $this->getProviderByMobileOrEmailOrID($request->mobile, '', '', $request->type);
            if (!$provider) {
                return $this->returnError('E001', trans('messages.No provider with this id'));
            }
            DB::beginTransaction();
            try {
                $activationCode = (string)rand(1000, 9999);
                $message = trans('messages.Your Activation Code') . ' ' . $activationCode;
                $this->sendSMS(!empty($request->mobile) ? $request->mobile : $provider->mobile, $message);

                $provider->update([
                    'activation_code' => $activationCode,
                    //'activation' => 0,
                ]);
                DB::commit();

                if ($provider->api_token == null or $provider->api_token == '' or !$provider->api_token) {
                    $tempToken = $this->getRandomString(250);
                    $provider->update(['api_token' => $tempToken]);
                }
                return $this->returnData('provider', json_decode(json_encode($provider, JSON_FORCE_OBJECT)), trans('messages.confirm code send'));

            } catch (\Exception $ex) {
                DB::rollback();
                return $this->returnError($ex->getCode(), $ex->getMessage());
            }

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
        $chkCode = Provider::where('api_token', $string)->first();
        if ($chkCode) {
            $this->getRandomString(250);
        }
        return $string;
    }

    public function resetPassword(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "password" => "required|max:255|confirmed",
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $provider = $this->getData($request->api_token);
            if ($provider == null)
                $provider = $this->getDataByLastToken($request->api_token);
            if ($provider == null)
                return $this->returnError('E001', trans('messages.Provider not found'));

            DB::beginTransaction();

            try {
                $provider->update([
                    'password' => $request->password,
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

    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|numeric",
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $provider = $this->getProviderByID($request->id);
            if ($provider != null)
                return $this->returnData('provider', json_decode(json_encode($provider, JSON_FORCE_OBJECT)));
            return $this->returnError('E001', trans('messages.No provider with this id'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function prepare_update_provider_profile(Request $request)
    {
        try {
            $provider = $this->auth('provider-api');
            $provider_relation = Provider::with(['city' => function ($city) {
                $city->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }, 'district' => function ($distric) {
                $distric->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }])->find($provider->id);

            $provider_relation->makeHidden(['provider_has_bill', 'adminprices', 'longitude', 'latitude', 'email', 'address', 'street', 'provider_id', 'branch_no', 'paid_balance', 'unpaid_balance', 'rate', 'hide', 'parent_type'])->toArray();
            $provider_relation->makeVisible(['type_id']);

            if (!$provider_relation) {
                return $this->returnError('D000', trans('messages.User not found'));
            }
            return $this->returnData('provider', json_decode(json_encode($provider_relation, JSON_FORCE_OBJECT)));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function update_provider_profile(Request $request)
    {
        $provider = $this->auth('provider-api');

        if (!$provider) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        //main provider
        if ($provider->provider_id == null) {
            $validator = Validator::make($request->all(), [
                "name_en" => "required|max:255",
                "name_ar" => "required|max:255",
                "username" => 'required|string|max:100|unique:providers,username,' . $provider->id . ',id',
                "commercial_no" => 'required|unique:providers,commercial_no,' . $provider->id,
                "password" => "max:255",
                "old_password" => "required_with:password",
                "city_id" => "required|exists:cities,id",
                "district_id" => "required|exists:districts,id",
                "mobile" => array(
                    "required",
                    "numeric",
                    //   Rule::unique('providers', 'mobile')->ignore($provider->id),
                    "digits_between:8,10",
                    "regex:/^(009665|9665|\+9665|05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/"
                )
            ]);
        } else {
            //branch
            $validator = Validator::make($request->all(), [
                "password" => "required|max:255",
                "old_password" => "required_with:password",
                "username" => 'required|string|max:100|unique:providers,username,' . $provider->id . ',id',
                "mobile" => array(
                    "required",
                    "numeric",
                    // Rule::unique('providers', 'mobile')->ignore($provider->id),
                    "digits_between:8,10",
                    "regex:/^(009665|9665|\+9665|05|5)(5|0|3|6|4|9|1|8|7)([0-9]{7})$/"
                )
            ]);
        }
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        DB::beginTransaction();

        if (isset($request->mobile)) {
            if ($provider->provider_id != null) {  //branch
                $exists = $this->checkIfMobileExistsForOtherBranches($request->mobile);
                if ($exists) {
                    $proMobile = Provider::whereNotNull('provider_id')->where('mobile', $request->mobile)->first();
                    if ($proMobile->id != $provider->id)
                        return $this->returnError('D000', trans("messages.phone number used before"));
                }
            }
            if ($provider->provider_id == null) {  //main provider
                $exists = $this->checkIfMobileExistsForOtherProviders($request->mobile);
                if ($exists) {
                    $proMobile = Provider::where('provider_id', null)->where('mobile', $request->mobile)->first();
                    if ($proMobile->id != $provider->id)
                        return $this->returnError('D000', trans("messages.phone number used before"));
                }
            }

            if ($request->mobile != $provider->mobile) {
                $activationCode = (string)rand(1000, 9999);
                $message = trans('messages.Your Activation Code') . ' ' . $activationCode;
                $this->sendSMS(!empty($request->mobile) ? $request->mobile : $provider->mobile, $message);
                $provider->update([
                    'mobile' => !empty($request->mobile) ? $request->mobile : $provider->mobile,
                    'activation' => 0,
                    'activation_code' => $activationCode,
                ]);
            }
        }
        $fileName = $provider->logo;
        if (isset($request->logo) && !empty($request->logo)) {
            $fileName = $this->saveImage('providers', $request->logo);
        }
        $commercial_no = $request->commercial_no ? $request->commercial_no : $provider->commercial_no;
        if ($request->password) {

            //check for old password
            if (Hash::check($request->old_password, $provider->password)) {
                $provider->update([
                    'name_en' => $request->name_en ? $request->name_en : $provider->name_en,
                    'name_ar' => $request->name_ar ? $request->name_ar : $provider->name_ar,
                    "username" => $request->username,
                    'commercial_no' => $commercial_no,
                    "city_id" => $request->city_id,
                    "district_id" => $request->district_id,
                    'password' => $request->password,
                    'logo' => $fileName,
                ]);
            } else {

                return $this->returnError('E002', trans('messages.invalid old password'));
            }

        } else {
            $provider->update([
                'name_en' => $request->name_en ? $request->name_en : $provider->name_en,
                'name_ar' => $request->name_ar ? $request->name_ar : $provider->name_ar,
                "username" => $request->username,
                'commercial_no' => $commercial_no,
                "city_id" => $request->city_id,
                "district_id" => $request->district_id,
                'logo' => $fileName,
            ]);
        }

        $provider->makeVisible(['activation', 'status']);
        //update all brnaches with provider logo
        $provider->providers()->update(['logo' => $fileName]);
        DB::commit();
        return $this->returnData('provider', json_decode(json_encode($provider, JSON_FORCE_OBJECT)),
            trans('messages.Provider data updated successfully'));

        // $provider = $this->getAllData($provider->api_token, $activation);

    }


    public
    function getCoachTeams(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:coahes,id",
        ]);
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $provider = $this->getProviderByID($request->id);

        if ($provider != null) {
            if ($provider->provider_id != null) {
                $request->provider_id = 0;
                $branchesIDs = [$provider->id];
            } else {
                $branchesIDs = $provider->providers()->pluck('id');
            }

            if (count($branchesIDs) > 0) {
                if (isset($request->specification_id) && $request->specification_id != 0) {
                    if ($validation->specification_found == null)
                        return $this->returnError('D000', trans('messages.There is no specification with this id'));
                }
                if (isset($request->nickname_id) && $request->nickname_id != 0) {
                    if ($validation->nickname_found == null)
                        return $this->returnError('D000', trans('messages.There is no nickname with this id'));
                }
                if (isset($request->provider_id) && $request->provider_id != 0) {
                    if ($validation->provider_found == null)
                        return $this->returnError('D000', trans('messages.There is no branch with this id'));

                    if ($validation->branch_found)
                        return $this->returnError('D000', trans("messages.This branch isn't in your branches"));
                }
                if (isset($request->gender) && $request->gender != 0 && !in_array($request->gender, [1, 2])) {
                    return $this->returnError('D000', trans("messages.This is invalid gender"));
                }

                $front = $request->has('show_front') ? 1 : 0;
                $doctors = $this->getDoctors($branchesIDs, $request->specification_id, $request->nickname_id, $request->provider_id, $request->gender, $front);

                if (count($doctors) > 0) {
                    foreach ($doctors as $key => $doctor) {
                        $doctor->time = "";
                        $days = $doctor->times;
                        $match = $this->getMatchedDateToDays($days);

                        if (!$match || $match['date'] == null) {
                            $doctor->time = new \stdClass();;
                            continue;
                        }
                        $doctorTimesCount = $this->getDoctorTimePeriodsInDay($match['day'], $match['day']['day_code'], true);
                        $availableTime = $this->getFirstAvailableTime($doctor->id, $doctorTimesCount, $days, $match['date'], $match['index']);
                        $doctor->time = $availableTime;
                        $doctor->branch_name = Doctor::find($doctor->id)->provider->{'name_' . app()->getLocale()};
                    }
                    $total_count = $doctors->total();
                    $doctors->getCollection()->each(function ($doctor) {
                        $doctor->makeVisible(['name_en', 'name_ar', 'information_en', 'information_ar']);
                        return $doctor;
                    });


                    $doctors = json_decode($doctors->toJson());
                    $doctorsJson = new \stdClass();
                    $doctorsJson->current_page = $doctors->current_page;
                    $doctorsJson->total_pages = $doctors->last_page;
                    $doctorsJson->total_count = $total_count;
                    $doctorsJson->data = $doctors->data;
                    return $this->returnData('doctors', $doctorsJson);
                }
                return $this->returnData('doctors', $doctors);
            }
            return $this->returnError('E001', trans('messages.There are no branches for this provider'));
        }
        return $this->returnError('E001', trans('messages.There is no provider with this id'));

    }

    public
    function getProviderTypes()
    {
        try {
            $types = $this->getAllProviderTypes();
            if (count($types) > 0)
                return $this->returnData('types', $types);

            return $this->returnError('E001', trans('messages.There are no provider types found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function activateAccount(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "activation_code" => "required|max:255",
                "api_token" => "required",
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $provider = $this->getData($request->api_token);

            if ($provider == null)
                $provider = $this->getDataByLastToken($request->api_token);
            if ($provider == null)
                return $this->returnError('E001', trans('messages.Provider not found'));

            //if ($provider->activation)
            //  return $this->returnError('E0103', trans("messages.This provider already activated"));

            if ($provider->activation_code != $request->activation_code)
                return $this->returnError('E001', trans('messages.This code is not valid please enter it again'));

            $provider->activation = 1;
            // $provider->status = 0;   // need to approved by admin
            $provider->update();
            $provider->name = $provider->getTranslatedName();
            $provider->makeVisible(['api_token', 'activation', 'status', 'name_en', 'name_ar']);
            if ($provider->status == 0 && $provider->provider_id == null)
                return $this->returnData('provider', json_decode(json_encode($provider, JSON_FORCE_OBJECT)), app('settings')->{'approve_message_' . app()->getLocale()});
            else
                return $this->returnData('provider', json_decode(json_encode($provider, JSON_FORCE_OBJECT)), ' تم تفعيل  رقم الهاتف بنجاح ');

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function resendActivation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "api_token" => "required"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $provider = $this->getData($request->api_token);
            if ($provider == null)
                return $this->returnError('E001', trans('messages.Provider not found'));

            if ($provider->activation)
                return $this->returnError('E0103', trans("messages.This provider already activated"));

            if ($provider->no_of_sms == 3)
                return $this->returnError('E001', trans('messages.You exceed the limit of resending activation messages'));

            // resend code again
            $activationCode = (string)rand(1000, 9999);
            $provider->activation_code = $activationCode;
            $provider->no_of_sms = ($provider->no_of_sms + 1);
            $provider->update();
            $provider->name = $provider->getTranslatedName();
            $provider->makeVisible(['activation_code', 'activation', 'status', 'name_en', 'name_ar']);

            $message = trans('messages.Your Activation Code') . ' ' . $activationCode;
            $this->sendSMS($provider->mobile, $message);

            return $this->returnData('provider', json_decode(json_encode($provider, JSON_FORCE_OBJECT)));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function reportingComment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "reservation_no" => "required|max:191",
            "reporting_type_id" => "required",
        ]);

        $provider = $this->auth('provider-api');
        if ($provider == null)
            return $this->returnError('E001', trans("messages.Provider not found"));

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $reservation = Reservation::where('reservation_no', $request->reservation_no)->first();
        if ($reservation == null)
            return $this->returnError('D000', trans('messages.No reservation with this number'));

        $reporting_type = ReportingType::find($request->reporting_type_id);
        if ($reporting_type == null)
            return $this->returnError('D000', trans('messages.No reporting type with this id'));

        CommentReport::create(['provider_id' => $provider->id, 'reservation_no' => $request->reservation_no, 'reporting_type_id' => $request->reporting_type_id]);
        return $this->returnSuccessMessage(trans('messages.Comment Reported  successfully'));
    }

    public
    function getCurrentReservations()
    {
        try {
            $provider = $this->auth('provider-api');
            $provider->makeVisible(['application_percentage_bill']);
            $provider_has_bill = 0;

            if ($provider->provider_id == null) { // provider
                if (!is_numeric($provider->application_percentage_bill) || $provider->application_percentage_bill == 0) {
                    $provider_has_bill = 0;
                } else {
                    $provider_has_bill = 1;
                }
                $branches = $provider->providers()->pluck('id')->toArray();
                array_unshift($branches, $provider->id);

            } else {
                $branches = [$provider->id];
                $mainProv = Provider::find($provider->provider_id);

                if (!is_numeric($mainProv->application_percentage_bill) || $mainProv->application_percentage_bill == 0) {
                    $provider_has_bill = 0;
                } else {
                    $provider_has_bill = 1;
                }
            }

            $reservations = $this->AcceptedReservations($branches);

            if (count($reservations->toArray()['data']) > 0) {
                $total_count = $reservations->total();
                $reservations = json_decode($reservations->toJson());
                foreach ($reservations->data as $reservation) {   // toggle to know if provider has bill tax to apply
                    $reservation->provider_has_bill = $provider_has_bill;
                }

                $reservationsJson = new \stdClass();
                $reservationsJson->current_page = $reservations->current_page;
                $reservationsJson->total_pages = $reservations->last_page;
                $reservationsJson->total_count = $total_count;
                $reservationsJson->data = $reservations->data;

                return $this->returnData('reservations', $reservationsJson);
            }
            return $this->returnData('reservations', $reservations);
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function getNewReservations()
    {
        try {
            $provider = $this->auth('provider-api');

            if ($provider->provider_id == null) { // provider
                $branches = $provider->providers()->pluck('id')->toArray();
                array_unshift($branches, $provider->id);
            } else {
                $branches = [$provider->id];
            }

            $reservations = $this->NewReservations($branches);

            if (count($reservations->toArray()['data']) > 0) {
                $total_count = $reservations->total();
                $reservations = json_decode($reservations->toJson());
                $reservationsJson = new \stdClass();
                $reservationsJson->current_page = $reservations->current_page;
                $reservationsJson->total_pages = $reservations->last_page;
                $reservationsJson->total_count = $total_count;
                $reservationsJson->data = $reservations->data;

                return $this->returnData('reservations', $reservationsJson);
            }
            return $this->returnData('reservations', $reservations);
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public
    function AcceptReservation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "reservation_no" => "required|max:255",
                "price" => "numeric"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            DB::beginTransaction();
            $provider = $this->auth('provider-api');
            $reservation = $this->getReservationByNo($request->reservation_no, $provider->id);
            if ($reservation == null)
                return $this->returnError('D000', trans('messages.No reservation with this number'));

            if ($reservation->approved == 1)
                return $this->returnError('E001', trans('messages.Reservation already approved'));

            if ($reservation->approved == 2)
                return $this->returnError('E001', trans('messages.Reservation already rejected'));

            if (strtotime($reservation->day_date) < strtotime(Carbon::now()->format('Y-m-d')) ||
                (strtotime($reservation->day_date) == strtotime(Carbon::now()->format('Y-m-d')) &&
                    strtotime($reservation->to_time) < strtotime(Carbon::now()->format('H:i:s')))
            ) {

                return $this->returnError('E001', trans("messages.You can't take action to a reservation passed"));
            }
            if ($reservation->use_insurance) {
                if (!isset($request->price) || $request->price == 0 || empty($request->price))
                    return $this->returnError('E001', trans("messages.Price is required"));

                if ($reservation->price < $request->price)
                    return $this->returnError('E001', trans("messages.New price is larger than reservation price"));

                $reservation->update([
                    'price' => $request->price,
                    'approved' => 1
                ]);
            } else {
                $reservation->update([
                    'approved' => 1
                ]);
            }

            if ($reservation->user->email != null)
                Mail::to($reservation->user->email)->send(new AcceptReservationMail($reservation->reservation_no));
            DB::commit();
            try {
                $name = 'name_' . app()->getLocale();


                $bodyProvider = __('messages.approved user reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider -> provider ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()} ";

                $bodyUser = __('messages.approved your reservation') . " " . "{$provider -> provider ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()} ";

                //send push notification
                (new \App\Http\Controllers\NotificationController(['title' => __('messages.Reservation Status'), 'body' => $bodyProvider]))->sendProvider(Provider::find($provider->provider_id == null ? $provider->id : $provider->provider_id));

                (new \App\Http\Controllers\NotificationController(['title' => __('messages.Reservation Status'), 'body' => $bodyUser]))->sendUser($reservation->user);

                //send mobile sms

                $message = $bodyUser;
                $this->sendSMS($reservation->user->mobile, $message);

            } catch (\Exception $ex) {

            }
            return $this->returnSuccessMessage(trans('messages.Reservation approved successfully'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public
    function getBalance()
    {
        try {
            $provider = $this->auth('provider-api');
            if ($provider->provider_id == null) {
                $branches = $provider->providers()->pluck('id')->toArray();
                $balance = $this->getProviderBalance($branches);
                $allBalance = $this->sumBalance($branches);
            } else {
                $balance = $this->getProviderBalance([$provider->id]);
                $allBalance = $this->sumBalance([$provider->id]);
            }
            if (count($balance->toArray()) > 0) {
                $total_count = $balance->total();
                $balance->getCollection()->each(function ($bala) {
                    $bala = $bala->makeVisible(['balance']);
                    $bala->balance = number_format((float)$bala->balance, 2, '.', '');
                    return $bala;
                });

                $balance = json_decode($balance->toJson());
                $balanceJson = new \stdClass();
                $balanceJson->current_page = $balance->current_page;
                $balanceJson->total_pages = $balance->last_page;
                $balanceJson->total_count = $total_count;
                $balanceJson->total_balance = number_format((float)$allBalance, 2, '.', '');
                $balanceJson->data = $balance->data;
                return $this->returnData('balances', $balanceJson);
            }
            return $this->returnError('E001', trans("messages.No balance founded"));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public
    function completeReservation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "reservation_no" => "required|max:255",
                "arrived" => "required|in:0,1"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            DB::beginTransaction();
            $provider = $this->auth('provider-api');
            $provider->makeVisible(['application_percentage']);
            $reservation = $this->getReservationByNo($request->reservation_no, $provider->id);
            if ($reservation == null)
                return $this->returnError('D000', trans('messages.No reservation with this number'));

            if ($reservation->approved == 3)
                return $this->returnError('E001', trans('messages.Reservation already Completed'));

            if ($reservation->approved == 2)
                return $this->returnError('E001', trans('messages.Reservation already rejected'));

            $complete = $request->arrived == 1 ? 1 : 0;

            if ($complete == 1) {
                $reservation->update([
                    'approved' => 3
                ]);

                $totalBill = 0;
                $mainProv = Provider::find($provider->provider_id == null ? $provider->id : $provider->provider_id);
                if (!is_numeric($mainProv->application_percentage_bill) || $mainProv->application_percentage_bill == 0) {
                    $provider_has_bill = 0;
                } else {
                    $provider_has_bill = 1;
                }

                // get bill total only if discount apply to this provider and the reservation without coupons
                if ($provider_has_bill == 1 && $reservation->promocode_id == null) {
                    if (!$request->has('bill_total')) {
                        if ($request->bill_total <= 0) {
                            return $this->returnError('E001', trans('messages.Must add Bill Total'));
                        } else {
                            $totalBill = $request->bill_total;
                        }
                    }
                }

                $reservation->update([
                    'approved' => 3,
                    'bill_total' => $request->bill_total,
                    //'discount_type'   =>  $discountType
                ]);

                // Calculate the balance
                $this->calculateBalance($provider, $reservation->payment_method_id, $reservation);

                if ($reservation->user->email != null)
                    Mail::to($reservation->user->email)->send(new AcceptReservationMail($reservation->reservation_no));
            } else {
                $reservation->update([
                    'approved' => 2
                ]);
                if ($reservation->user->email != null)
                    Mail::to($reservation->user->email)->send(new   RejectReservationMail($reservation->reservation_no));
            }

            DB::commit();
            try {
                $name = 'name_' . app()->getLocale();

                $bill = false; // toggle to send true or false bill to fcm notification to redirect user after click notification to specific screen

                if ($provider->provider_id != null) {
                    if ($complete == 1 && $provider_has_bill == 1 && $reservation->promocode_id == null) {
                        $bodyProvider = __('messages.complete user reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider -> provider ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()}  ";
                        $bodyUser = __('messages.complete your reservation') . " " . "{$provider -> provider ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()}  - " . __('messages.rate provider and doctor and upload the bill');
                        $bill = false;
                    } elseif ($complete == 1) { //when reservation complete and user arrivred to branch and bill total entered
                        $bodyProvider = __('messages.complete user reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider -> provider ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()}  ";
                        $bodyUser = __('messages.complete your reservation') . " " . "{$provider -> provider ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()}  - ";
                    } else {
                        $bodyProvider = __('messages.canceled your reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider -> provider ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()} ";
                        $bodyUser = __('messages.canceled your reservation') . " " . "{$provider -> provider ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()} ";
                    }

                } else {
                    if ($complete == 1 && $provider_has_bill == 1 && $reservation->promocode_id == null) {
                        $bodyProvider = __('messages.complete user reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider  ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()}  ";
                        $bodyUser = __('messages.complete your reservation') . " " . "{$provider  ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()}  - " . __('messages.rate provider and doctor and upload the bill');
                        $bill = false;
                    } elseif ($complete == 1) { //when reservation complete and user arrivred to branch and bill total entered
                        $bodyProvider = __('messages.complete user reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()}  ";
                        $bodyUser = __('messages.complete your reservation') . " " . "{$provider -> provider ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()}  - ";
                    } else {
                        $bodyProvider = __('messages.canceled your reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider  ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()} ";
                        $bodyUser = __('messages.canceled your reservation') . " " . "{$provider  ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()} ";
                    }

                }

                //send push notification
                (new \App\Http\Controllers\NotificationController(['title' => __('messages.Reservation Status'), 'body' => $bodyProvider]))->sendProvider(Provider::find($provider->provider_id == null ? $provider->id : $provider->provider_id));

                (new \App\Http\Controllers\NotificationController(['title' => __('messages.Reservation Status'), 'body' => $bodyUser]))->sendUser($reservation->user, $bill, $reservation->id);

                //send mobile sms
                $message = $bodyUser;
                $this->sendSMS($reservation->user->mobile, $message);

            } catch (\Exception $ex) {

            }
            return $this->returnSuccessMessage(trans('messages.Reservation approved successfully'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function RejectReservation(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "reservation_no" => "required|max:255",
                "reason" => "required|exists:reasons,id"  // id of the reason
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            DB::beginTransaction();
            $provider = $this->auth('provider-api');
            $reservation = $this->getReservationByNo($request->reservation_no, $provider->id);

            if ($reservation == null)
                return $this->returnError('D000', trans('messages.No reservation with this number'));

            if ($reservation->approved == 1)
                return $this->returnError('E001', trans("messages.You can't reject approved reservation"));

            if ($reservation->approved == 2)
                return $this->returnError('E001', trans('messages.Reservation already rejected'));

            $reservation->update([
                'approved' => 2,
                'rejection_reason' => $request->reason
            ]);

            if ($reservation->user->email != null)
                Mail::to($reservation->user->email)->send(new RejectReservationMail($reservation->reservation_no, $reservation->rejection_reason));

            DB::commit();
            try {

                $name = 'name_' . app()->getLocale();
                $bodyProvider = __('messages.canceled user reservation') . "  {$reservation->user->name}   " . __('messages.in') . " {$provider -> provider ->  $name } " . __('messages.branch') . " - {$provider->getTranslatedName()} ";

                $bodyUser = __('messages.canceled your reservation') . " " . "{$provider -> provider ->  $name } " . __('messages.branch') . "  - {$provider->getTranslatedName()} ";

                //send push notification
                (new \App\Http\Controllers\NotificationController(['title' => __('messages.Reservation Status'), 'body' => $bodyProvider]))->sendProvider(Provider::find($provider->provider_id == null ? $provider->id : $provider->provider_id));

                (new \App\Http\Controllers\NotificationController(['title' => __('messages.Reservation Status'), 'body' => $bodyUser]))->sendUser($reservation->user);

                //send mobile sms

                $message = $bodyUser;
                $this->sendSMS($reservation->user->mobile, $message);

            } catch (\Exception $ex) {

            }
            return $this->returnSuccessMessage(trans('messages.Reservation rejected successfully'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public
    function ReservationDetails(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "reservation_no" => "required|max:255"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $provider = $this->auth('provider-api');
            $reservation = $this->getReservationByNoWihRelation($request->reservation_no, $provider->id);
            if ($reservation == null)
                return $this->returnError('E001', trans('messages.No reservation with this number'));

            return $this->returnData('reservation', $reservation);
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


//method for front end only allow main provider to show reservation details
    public
    function ReservationDetailsFront(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "reservation_no" => "required|max:255"
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $provider = $this->auth('provider-api');
            $reservation = Reservation::where('reservation_no', $request->reservation_no)->first();
            if (!$reservation)
                return $this->returnError('E001', trans('messages.No reservation with this number'));

            // reservation branch and provider
            $branch = Provider::find($reservation->provider_id);
            $branchId = $branch->id;
            $mainProviderId = $branch->provider->id;

            //check if auth provider is own of this reservation or not
            if ($provider->id != $mainProviderId) {
                return $this->returnError('E001', trans('messages.Cannot view reservation details'));
            }

            $reservation = $this->getReservationByNoWihRelationFront($request->reservation_no, $branchId);
            if ($reservation == null)
                return $this->returnError('E001', trans('messages.No reservation with this number'));

            return $this->returnData('reservation', $reservation);
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

//
    public
    function getTickets(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "actor_type" => "required|in:1,2",
                "type" => "sometimes|nullable"
            ]);


            DB::beginTransaction();
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $type = null;
            if ($request->has('type')) {
                if ($request->type != 1 && $request->type != 2 && $request->type != 3 && $request->type != 4) {
                    return $this->returnError('D000', trans('messages.Type Not Found'));
                }
                $type = $request->type;
            }

            $actor_type = $request->actor_type;

            if ($actor_type == 1 or $actor_type == '1') {
                $user = $this->auth('provider-api');
                if (!$user) {
                    return $this->returnError('D000', trans('messages.User not found'));
                }
                $messages = $this->getProviderMessages($user->id, $type);
            } else {
                $user = $this->auth('user-api');
                if (!$user) {
                    return $this->returnError('D000', trans('messages.User not found'));
                }
                $messages = $this->getUserMessages($user->id, $type);
            }


            if (count($messages->toArray()) > 0) {
                $total_count = $messages->total();
                $messages->getCollection()->each(function ($message) {

                    $replayCount = Replay::where('ticket_id', $message->id)->where('FromUser', 0)->count();   // user 0 means replay from admin
                    $lastReplay = Replay::where('ticket_id', $message->id)->orderBy('created_at', 'DESC')->first();   // user 0 means replay from admin

                    if ($replayCount == 0) {
                        $message->replay_status = 0;  // بانتظار الرد
                    } else {
                        $message->replay_status = 1;    //   تم الرد
                    }


                    if ($message->solved == 0) {
                        $message->solved = 0;
                    } else {
                        $message->solved = 1;
                    }

                    $message->last_replay = $lastReplay->message;
                    unset($message->actor_type);


                    if ($message->importance == 1)
                        $message->importance_text = trans('messages.Quick');
                    else if ($message->importance == 2)
                        $message->importance_text = trans('messages.Normal');

                    if ($message->type == 1)
                        $message->type_text = trans('messages.Inquiry');

                    else if ($message->type == 2)
                        $message->type_text = trans('messages.Suggestion');

                    else if ($message->type == 3)
                        $message->type_text = trans('messages.Complaint');

                    else if ($message->type == 4)
                        $message->type_text = trans('messages.Others');

                    return $message;
                });

                $messages = json_decode($messages->toJson());
                $messagesJson = new \stdClass();
                $messagesJson->current_page = $messages->current_page;
                $messagesJson->total_pages = $messages->last_page;
                $messagesJson->total_count = $total_count;
                $messagesJson->data = $messages->data;
                return $this->returnData('messages', $messagesJson);
            }
            return $this->returnError('E001', trans("messages.No messages founded"));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    public
    function newTicket(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "importance" => "numeric|min:1|max:2",
            "type" => "numeric|min:1|max:4",
            "message" => "required",
            "title" => 'required',
            "actor_type" => "required|in:1,2"
        ]);
        DB::beginTransaction();
        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }

        $actor_type = $request->actor_type;

        if ($actor_type == 1 or $actor_type == '1') {
            $user = $this->auth('provider-api');
        } else if ($actor_type == 2 or $actor_type == '2') {
            $user = $this->auth('user-api');
        }

        if (!$user) {
            return $this->returnError('D000', trans('messages.User not found'));
        }

        if (!isset($request->title) || empty($request->title))
            return $this->returnError('D000', trans('messages.Please enter message title'));

        if (!isset($request->type) || $request->type == 0 || !isset($request->importance) || $request->importance == 0)
            return $this->returnError('D000', trans('messages.Please enter importance and type'));

        $ticket = Ticket::create([

            'title' => $request->title ? $request->title : "",
            'actor_id' => $user->id,
            'actor_type' => $actor_type,
            'message_no' => 'M' . $user->id . uniqid(),
            'type' => $request->type,
            'importance' => $request->importance,
            'message' => $request->message,
            //'message_id' => $request->message_id != 0 ? $request->message_id : NULL,
            //'order' => $order
        ]);


        $replay = [
            "ticket_id" => $ticket->id,
            "message" => $request->message,
            "FromUser" => $actor_type
        ];

        $replay = new Replay($replay);

        $ticket->replays()->save($replay);

        $appData = $this->getAppInfo();
        // Sending mail to manager
        /* if($request->message_id != null && $request->message_id != 0)
             Mail::to($appData->email)->send(new NewReplyMessageMail($user->name_ar));*/

        //  Mail::to($appData->email)->send(new NewUserMessageMail($user->name_ar));

        DB::commit();
        /* if($request->message_id != null && $request->message_id != 0)
             return $this->returnSuccessMessage(trans('messages.Reply send successfully'));*/

        return $this->returnSuccessMessage(trans('messages.Message sent successfully, you can keep in touch with replies by view messages page'));


    }

    public
    function AddMessage(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|exists:tickets,id",
                "message" => "required",
                "actor_type" => "required|in:1,2"
            ]);

            DB::beginTransaction();
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $actor_type = $request->actor_type;

            if ($actor_type == 1 or $actor_type == '1')
                $user = $this->auth('provider-api');


            if ($actor_type == 2 or $actor_type == '2')
                $user = $this->auth('user-api');

            $id = $request->id;
            $message = $request->message;
            $ticket = Ticket::find($id);

            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }

            if ($ticket) {
                if ($ticket->actor_id != $user->id) {
                    return $this->returnError('D000', trans('messages.cannot replay for this converstion'));
                }
            }

            Replay::create([
                'message' => $message,
                "ticket_id" => $id,
                "FromUser" => $actor_type
            ]);


            $appData = $this->getAppInfo();
            // Sending mail to manager
            /* if($request->message_id != null && $request->message_id != 0)
                 Mail::to($appData->email)->send(new NewReplyMessageMail($user->name_ar));*/

            //  Mail::to($appData->email)->send(new NewUserMessageMail($user->name_ar));

            DB::commit();
            /* if($request->message_id != null && $request->message_id != 0)
                 return $this->returnSuccessMessage(trans('messages.Reply send successfully'));*/

            return $this->returnSuccessMessage(trans('messages.Reply send successfully'));


        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }


    public
    function GetTicketMessages(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "id" => "required|exists:tickets,id",
                "actor_type" => "required|in:1,2"
            ]);

            DB::beginTransaction();
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $actor_type = $request->actor_type;

            if ($actor_type == 1 or $actor_type == '1')
                $user = $this->auth('provider-api');


            if ($actor_type == 2 or $actor_type == '2')
                $user = $this->auth('user-api');

            $id = $request->id;
            $ticket = Ticket::find($id);
            if (!$user) {
                return $this->returnError('D000', trans('messages.User not found'));
            }

            if ($ticket) {
                if ($ticket->actor_id != $user->id) {
                    return $this->returnError('D000', trans('messages.cannot access this converstion'));
                }
            }

            $messages = Replay::where('ticket_id', $id)->paginate(10);

            if (count($messages->toArray()) > 0) {

                $total_count = $messages->total();

                $messages = json_decode($messages->toJson());
                $messagesJson = new \stdClass();
                $messagesJson->current_page = $messages->current_page;
                $messagesJson->total_pages = $messages->last_page;
                $messagesJson->total_count = $total_count;
                $messagesJson->data = $messages->data;
                //add photo
                foreach ($messages->data as $message) {
                    if ($message->FromUser == 0) {//admin
                        $message->logo = url('/') . '/images/admin.png';
                    } elseif ($message->FromUser == 1) { //provider
                        $ticket = Ticket::find($id);
                        if ($ticket) {
                            $logo = Provider::where('id', $ticket->actor_id)->value('logo');
                            $message->logo = $logo;
                        } else {
                            $message->logo = url('/') . '/images/admin.png';  // default image
                        }
                    } elseif ($message->FromUser == 2) { //user
                        $message->logo = url('/') . '/images/male.png';
                    } else {
                        $message->logo = url('/') . '/images/admin.png';  // default image
                    }
                }
                return $this->returnData('messages', $messagesJson);
            }

            return $this->returnError('E001', trans("messages.No messages founded"));

            DB::commit();


        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }


    }

    public
    function addUserRecord(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "reservation_no" => "required|max:255",
            "attachments" => "required|array|between:1,10",
            "attachments.*.file" => "required",
            "attachments.*.category_id" => "required|exists:categories,id",
            "summary" => "required"
        ]);

        if ($validator->fails()) {
            $code = $this->returnCodeAccordingToInput($validator);
            return $this->returnValidationError($code, $validator);
        }
        DB::beginTransaction();
        $provider = $this->auth('provider-api');
        $reservation = $this->getReservationByNo($request->reservation_no, $provider->id);

        if ($reservation == null)
            return $this->returnError('D000', trans('messages.There is no reservation with this id'));

        if ($reservation->for_me == '0')
            return $this->returnError('E001', trans("messages.You can't add record to person related to user"));

        $provider_id = $this->getMainProvider($provider->id);

        $record = UserRecord::create([
            'user_id' => $reservation->user_id,
            'reservation_no' => $reservation->reservation_no,
            'specification_id' => $reservation->doctor->specification_id,
            'day_date' => $reservation->day_date,
            'summary' => $request->summary,
            'provider_id' => $provider_id,
            'doctor_id' => $reservation->doctor_id
        ]);
        $user_attachments = [];
        foreach ($request->attachments as $attachment) {
            $path = $this->saveImage('users', $attachment['file']);
            $user_attachments[] = [
                'user_id' => $reservation->user_id,
                'record_id' => $record->id,
                'category_id' => $attachment['category_id'],
                'attachment' => $path
            ];
        }
        UserAttachment::insert($user_attachments);
        DB::commit();
        return $this->returnSuccessMessage(trans('messages.User record uploaded successfully'));

    }


    protected
    function getMainProvider($id)
    {


        $branch = Provider::where('id', $id)->branch()->first();

        if ($branch) {

            return $branch->provider_id;
        }

        return $id;
    }


    public
    function logout(Request $request)
    {

        try {
            $provider = $this->auth('provider-api');
            $token = $request->api_token;
            Token::where('api_token', $token)->delete();
            $activationCode = (string)rand(1000, 9999);
            $provider->activation_code = $activationCode;
            $provider->update();
            return $this->returnData('message', trans('messages.Logged out successfully'));

        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }

    }

}
