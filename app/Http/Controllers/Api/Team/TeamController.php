<?php

namespace App\Http\Controllers\Api\Team;

use App\Http\Controllers\Controller;
use App\Models\Academy;
use App\Models\Coach;
use App\Models\Team;
use App\Models\Time;
use App\Models\Token;
use App\Traits\GlobalTrait;
use App\Traits\SMSTrait;
use App\Traits\TeamTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Traits\CoachTrait;
use Illuminate\Support\Facades\DB;
use Validator;
use Auth;
use JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class TeamController extends Controller
{
    use TeamTrait, GlobalTrait, SMSTrait;

    public function __construct(Request $request)
    {

    }

    public function getAllTeamsByAcademyCode(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "academy_code" => "required|exists:academies,code",
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }

            $academyId = Academy::where('code', $request->academy_code)->value('id');
            $teams = $this->getTeamsByAcademyId($academyId);
            if (count($teams) > 0)
                return $this->returnData('teams', $teams);

            return $this->returnError('E001', trans('messages.There are no teams found'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    public function getStudent(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                "id" => "required|exists:teams",
            ]);
            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $coach = $this->auth('coach-api');

            $students = $this->getStudentsInTeam($request->id,$request);

            if (count($students->toArray()) > 0) {
                $total_count = $students->total();
                $students = json_decode($students->toJson());
                $studentsJson = new \stdClass();
                $studentsJson->current_page = $students->current_page;
                $studentsJson->total_pages = $students->last_page;
                $studentsJson->total_count = $total_count;
                $studentsJson->data = $students->data;
                return $this->returnData('students', $studentsJson);
            }
            return $this->returnError('E001', trans('messages.There are no students in this teams'));
        } catch (\Exception $ex) {
            return $this->returnError($ex->getCode(), $ex->getMessage());
        }
    }

    function getTeamTimes(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                "team_id" => "required|exists:teams,id",
            ]);

            if ($validator->fails()) {
                $code = $this->returnCodeAccordingToInput($validator);
                return $this->returnValidationError($code, $validator);
            }
            $times = Time::where('team_id', $request->team_id)->get();
            return $this->returnData('times', $times);
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
