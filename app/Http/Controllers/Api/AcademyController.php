<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
 use App\Traits\AcademyTrait;
use App\Traits\GlobalTrait;
use Illuminate\Http\Request;
 use Validator;
use Auth;
use JWTAuth;

class AcademyController extends Controller
{
   use  AcademyTrait,GlobalTrait;

    public function __construct(Request $request)
    {

    }

    public function getAcademies(Request $request){
        try {
            $academies = $this->getAllAcademies();
            if (count($academies) > 0)
                return $this->returnData('academies', $academies);

            return $this->returnError('E001', trans('messages.There are no academies found'));
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
