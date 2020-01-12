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
            $teams = $this->getTeams($coach);
            if (count($teams->toArray()) > 0) {
                $total_count = $teams->total();
                $teams->getCollection()->each(function ($team) {
                    $team->name = $team->getTranslatedName();
                    $team->makeHidden(['pivot', 'academy_id', 'name_ar', 'name_en']);
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
            $coach_relation = Coach::with(['academy' => function ($city) {
                $city->select('id', DB::raw('name_' . app()->getLocale() . ' as name'));
            }])->find($coach->id);

            if (!$coach_relation) {
                return $this->returnError('D000', trans('messages.User not found'));
            }
            return $this->returnData('coach', json_decode(json_encode($coach_relation, JSON_FORCE_OBJECT)));

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
            "name_ar" => "required|max:255",
            "name_en" => "required|max:255",
            "mobile" => 'required|max:100|unique:coahes,mobile,' . $coach->id . ',id',
            "password" => "sometimes|nullable|confirmed|max:255",
            "password_confirmation" => "required_with:password",
            "old_password" => "required_with:password",
            "academy_id" => "required|exists:academies,id",
            "photo" => "sometimes|nullable|mimes:jpeg,jpg,png",
            "gender" => "required|in:1,2"
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
                    'name_en' => $request->name_en,
                    'name_ar' => $request->name_ar,
                    "mobile" => $request->mobile,
                    "academy_id" => $request->academy_id,
                    "gender" => $request->gender,
                    'password' => $request->password,
                    'photo' => $fileName
                ]);
            } else {
                return $this->returnError('E002', trans('messages.invalid old password'));
            }

        } else {
            $coach->update([
                'name_en' => $request->name_en,
                'name_ar' => $request->name_ar,
                "mobile" => $request->mobile,
                "academy_id" => $request->academy_id,
                "gender" => $request->gender,
                'photo' => $fileName
            ]);
         }
        $coach->makeVisible(['status']);
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
}
