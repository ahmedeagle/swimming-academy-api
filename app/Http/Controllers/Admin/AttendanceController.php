<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Validator;
use Hash;

class AttendanceController extends Controller
{
    use PublicTrait;

    public function index()
    {
        try {
            $academies = Academy::active()->select('id', 'name_ar as name')->get();
            $teams = Team::active()->selection()->get();
            return view('admin.attendance.index', compact('academies', 'teams'));
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    public function addTeamAttendance(Request $request)
    {
        return $request;
    }

    public function loadUsersByTeam(Request $request)
    {
         try {
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'exists' => 'هذه القيمه غير موجوده لدينا ',
                'date-format' => 'صيغة التاريخ غير مقبوله'
            ];

            $validator = Validator::make($request->all(), [
                'academy_id' => 'required|exists:academies,id',
                'category_id' => 'required|exists:categories,id',
                'team_id' => 'required|exists:teams,id',
                'date' => 'required|date-format:Y-m-d',
            ], $messages);

            if ($validator->fails()) {
                 return response()->json($validator->errors(), 422);
            }
            /* if (date("m", strtotime($request->date)) != date("m") or date("Y", strtotime($request->date)) != date("Y")) {
                 return response()->json(['date' => ['لايمكن تحديد الغياب والحضور الا للشهر الحالي فقط ']], 422);
             }*/
            $teamId = $request->team_id;
            $date = $request->date;
            $users = User::active()->with(['attendances' => function ($q) use ($date) {
                $q->whereDate('date', '=', $date);
            }])->whereHas('team', function ($q) use ($teamId) {
                $q->where('id', $teamId);
            })->get();
            $view = view('admin.users.users', compact('users'))->renderSections();
            return response()->json([
                'content' => $view['main'],
            ]);
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function times(Request $request)
    {
        try {
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'exists' => 'هذه القيمه غير موجوده لدينا ',
            ];

            $validator = Validator::make($request->all(), [
                'academy_id' => 'required|exists:academies,id',
                'category_id' => 'required|exists:categories,id',
                'team_id' => 'required|exists:teams,id',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $data=[];
            $data['team_id'] = $request->team_id;
            $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();
            $data['teams'] = Team::active()->selection()->get();
            $data['categories'] = Category::active()->get();

            return view('admin.attendance.times',$data);
        } catch (\Exception $ex) {
            return abort('404');
        }
    }
}
