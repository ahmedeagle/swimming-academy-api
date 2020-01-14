<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Traits\GlobalTrait;
use Illuminate\Http\Request;
use Validator;
use Auth;
use JWTAuth;
use DB;

class GeneralController extends Controller
{
    use GlobalTrait;

    public function __construct(Request $request)
    {

    }

    public
    function aboutUs(Request $request)
    {
        try {
            $aboutUs =   Setting::select(DB::raw('title_' . app()->getLocale() . ' as title'),DB::raw('content_' . app()->getLocale() . ' as content'))->first();
            if ($aboutUs)
                return $this->returnData('aboutUs', $aboutUs);

            return $this->returnError('E001', trans('messages.There is no data found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


}
