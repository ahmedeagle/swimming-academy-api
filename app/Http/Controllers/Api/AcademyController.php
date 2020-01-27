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
    use  AcademyTrait, GlobalTrait;

    public function __construct(Request $request)
    {

    }

    public function getAcademies(Request $request)
    {
        try {
            $academies = $this->getAllAcademies();
            if (count($academies) > 0)
                return $this->returnData('academies', $academies);

            return $this->returnError('E001', trans('messages.There are no academies found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }


    //get categories by academy id

    public function getAcademyCategories(Request $request)
    {

        try {
            $messages = [
                'academy_code.required' => __('messages.academyCodeRequired'),
                'academy_code.exists' => __('messages.academyCodeNotExist'),
            ];
            $rules = [
                'academy_code' => 'required|exists:academies,code'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

             $categories = $this->getAcademyCategoriesByCode($request->academy_code);
            if (isset($categories) && $categories->count() > 0)
                return $this->returnData('categories', $categories);

            return $this->returnError('E001', trans('messages.There are no categories found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getCategoryTeams(Request $request)
    {

        try {
            $messages = [
                'category_id.required' => __('messages.categoryRequired'),
                'category_id.exists' => __('messages.categoryNotExist'),
            ];
            $rules = [
                'category_id' => 'required|exists:categories,id'
            ];

            $validator = Validator::make($request->all(), $rules, $messages);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $teams = $this->getCategoryTeamsById($request->category_id);
            if (isset($teams) && $teams->count() > 0)
                return $this->returnData('teams', $teams);
            return $this->returnError('E001', trans('messages.There are no teams found'));
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
