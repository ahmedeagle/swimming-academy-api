<?php

namespace App\Traits;

use App\Models\Category;
use App\Models\Coach;
use App\Models\District;
use App\Models\Doctor;
use App\Models\Manager;
use App\Models\Message;
use App\Models\Mix;
use App\Models\City;
use App\Models\Nationality;
use App\Models\Nickname;
use App\Models\Payment;
use App\Models\PromoCodeCategory;
use App\Models\Provider;
use App\Models\ProviderType;
use App\Models\Reservation;
use App\Models\Specification;
use App\Models\User;
use App\Models\InsuranceCompany;
use App\Models\PaymentMethod;
use DB;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Carbon\Carbon;


trait GlobalTrait
{

    public function checkUser($id)
    {
        return User::find($id);
    }


    public function checkCoach($id)
    {
        return Coach::find($id);
    }


    public function getCurrentLang()
    {
        return app()->getLocale();
    }

    public function returnError($errNum, $msg)
    {
        return response()->json([
            'status' => false,
            'errNum' => $errNum,
            'msg' => $msg
        ]);
    }

    public function checkToken(Request $request)
    {
        try {
            $request->headers->set('Authorization', 'Bearer ' . $request->api_token);
            JWTAuth::parseToken()->authenticate();
            return true;
        } catch (\Exception $e) {
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function returnValidationError($code = "E001", $validator)
    {
        return $this->returnError($code, $validator->errors()->first());
    }

    public function returnSuccessMessage($msg = "", $errNum = "S000")
    {
        return ['status' => true, 'errNum' => $errNum, 'msg' => $msg];
    }

    public function returnData($key, $value, $msg = "")
    {
        return response()->json(['status' => true, 'errNum' => "S000", 'msg' => $msg, $key => $value]);
    }

    public function returnCodeAccordingToInput($validator)
    {
        $inputs = array_keys($validator->errors()->toArray());
        $code = $this->getErrorCode($inputs[0]);
        return $code;
    }

    public function getErrorCode($input)
    {
        if ($input == "name")
            return 'E0011';

        else if ($input == "password")
            return 'E002';

        else if ($input == "mobile")
            return 'E003';

        else if ($input == "id_number")
            return 'E004';

        else if ($input == "birth_date")
            return 'E005';

        else if ($input == "agreement")
            return 'E006';

        else if ($input == "email")
            return 'E007';

        else if ($input == "city_id")
            return 'E008';

        else if ($input == "insurance_company_id")
            return 'E009';

        else if ($input == "activation_code")
            return 'E010';

        else if ($input == "longitude")
            return 'E011';

        else if ($input == "latitude")
            return 'E012';

        else if ($input == "id")
            return 'E013';

        else if ($input == "promocode")
            return 'E014';

        else if ($input == "doctor_id")
            return 'E015';

        else if ($input == "payment_method" || $input == "payment_method_id")
            return 'E016';

        else if ($input == "day_date")
            return 'E017';

        else if ($input == "specification_id")
            return 'E018';

        else if ($input == "importance")
            return 'E019';

        else if ($input == "type")
            return 'E020';

        else if ($input == "message")
            return 'E021';

        else if ($input == "reservation_no")
            return 'E022';

        else if ($input == "reason")
            return 'E023';

        else if ($input == "branch_no")
            return 'E024';

        else if ($input == "name_en")
            return 'E025';

        else if ($input == "name_ar")
            return 'E026';

        else if ($input == "gender")
            return 'E027';

        else if ($input == "nickname_en")
            return 'E028';

        else if ($input == "nickname_ar")
            return 'E029';

        else if ($input == "rate")
            return 'E030';

        else if ($input == "price")
            return 'E031';

        else if ($input == "information_en")
            return 'E032';

        else if ($input == "information_ar")
            return 'E033';

        else if ($input == "street")
            return 'E034';

        else if ($input == "branch_id")
            return 'E035';

        else if ($input == "insurance_companies")
            return 'E036';

        else if ($input == "photo")
            return 'E037';

        else if ($input == "logo")
            return 'E038';

        else if ($input == "working_days")
            return 'E039';

        else if ($input == "insurance_companies")
            return 'E040';

        else if ($input == "reservation_period")
            return 'E041';

        else if ($input == "nationality_id")
            return 'E042';

        else if ($input == "commercial_no")
            return 'E043';

        else if ($input == "nickname_id")
            return 'E044';

        else if ($input == "reservation_id")
            return 'E045';

        else if ($input == "attachments")
            return 'E046';

        else if ($input == "summary")
            return 'E047';

        else if ($input == "user_id")
            return 'E048';

        else if ($input == "mobile_id")
            return 'E049';

        else if ($input == "paid")
            return 'E050';

        else if ($input == "use_insurance")
            return 'E051';

        else if ($input == "doctor_rate")
            return 'E052';

        else if ($input == "provider_rate")
            return 'E053';

        else if ($input == "message_id")
            return 'E054';

        else if ($input == "hide")
            return 'E055';

        else if ($input == "checkoutId")
            return 'E056';

        else
            return "";
    }

    public function getCodeByDay($dayName)
    {
        if ($dayName == "Saturday")
            return 'sat';

        else if ($dayName == "Sunday")
            return 'sun';

        else if ($dayName == "Monday")
            return 'mon';

        else if ($dayName == "Tuesday")
            return 'tue';

        else if ($dayName == "Wednesday")
            return 'wed';

        else if ($dayName == "Thursday")
            return 'thu';

        else if ($dayName == "Friday")
            return 'fri';

        else
            return null;
    }

    public function getDayByCode($code)
    {
        if ($code == "sat")
            return 'Saturday';

        else if ($code == "sun")
            return 'Sunday';

        else if ($code == "mon")
            return 'Monday';

        else if ($code == "tue")
            return 'Tuesday';

        else if ($code == "wed")
            return 'Wednesday';

        else if ($code == "thu")
            return 'Thursday';

        else if ($code == "fri")
            return 'Friday';

        else
            return null;
    }

    public function checkPaymentMethodByID($id)
    {
        $paymentMethod = PaymentMethod::find($id);
        if ($paymentMethod != null)
            return true;
        return false;
    }

    public function getPaymentMethodByID($id)
    {
        return PaymentMethod::find($id);
    }



    public function getAgreementText()
    {
        return Mix::select(DB::raw('agreement_' . $this->getCurrentLang() . ' as content'))->first();
    }

    public function getAllPaymentMethods()
    {
        return PaymentMethod::select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'))->get();
    }


    public function getLastReplyInMessage($id)
    {
        return Message::where('message_id', $id)->orderBy('order', 'DESC')->first();
    }

    public function saveImage($folder, $photo)
    {
        $img = str_replace('data:image/jpg;base64,', '', $photo);
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace('data:image/gif;base64,', '', $img);
        $img = str_replace('data:image/jpeg;base64,', '', $img);
        $img = str_replace(' ', '+', $img);
        $data = base64_decode($img);
        $filename = time() . '_' . $folder . '.png';
        $path = 'images/' . $folder . '/' . $filename;
        file_put_contents($path, $data);
        return 'images/' . $folder . '/' . $filename;
    }


    public function auth($guard, $relations = [])
    {
        $user = null;

        if (isset(request()->api_token)) {
            $api_token = request()->api_token;
            if ($guard == 'user-api') {
                 $user = User::whereHas('tokens', function ($q) use ($api_token) {
                    $q->where('api_token', $api_token);
                });
            } else if ($guard == 'coach-api') {
                $user = Coach::whereHas('tokens', function ($q) use ($api_token) {
                    $q->where('api_token', $api_token);
                });
            }
            if ($relations && is_array($relations))
                $user->with($relations);
            $user = $user->first();
        }
        return $user;
    }

}
