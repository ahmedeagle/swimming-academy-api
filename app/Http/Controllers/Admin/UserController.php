<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Coach;
use App\Models\Team;
use App\Models\TeamTime;
use App\Models\User;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Namshi\JOSE\Signer\SecLib\RS384;
use Validator;
use Hash;

class UserController extends Controller
{
    use PublicTrait;

    public function index()
    {
        $users = User::selection()->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $academies = Academy::active()->select('id', 'name_ar as name')->get();
        $teams = Team::active()->selection()->get();
        return view('admin.users.create', compact('academies', 'teams'));
    }


    public function store(Request $request)
    {

        $messages = [
            'name_ar.required' => 'أسم    الطالب  بالعربي  مطلوب  .',
            'name_en.required' => 'أسم  الطالب  بالانجليزي  مطلوب  .',
            'mobile.required' => 'رقم الهاتف مطلوب ',
            'mobile.unique' => 'رقم الهاتف مسجل لدينا من قبل ',
            'email.required' => 'البريد الالكتروني مطلوب ',
            'email.exists' => 'البريد الالكتروني  مسجل لدينا من قبل  ',
            'gender.required' => 'النوع مطلوب ',
            "password.required" => trans("admin/passwords.passwordRequired"),
            "password.confirmed" => trans("admin/passwords.confirmpassword"),
            "password.min" => trans("admin/passwords.confirmpassword"),
            'gender.in' => ' ألنوع مطلوب  ',
            'academy_id.required' => 'لابد من احتيار الاكاديمية اولا ',
            'academy_id.exists' => 'هذه الاكاديمية غير موجوده ',
            'photo.required' => 'لابد من رفع صوره  الطالب  ',
            'photo.mimes' => ' أمتداد الصوره غير مسموح به ',
            "teams.required" => 'لأابد من أختيار الفرق ',
            "teams.exists" => ' الفريق عير موجود لدينا  ',
            'birth_date.required' => 'تاريخ الميلاد مطلوب',
            'date-format' => ' صيغة التاريخ عير صحيحه لابد من ادخالها Y-m-d',
            "category_id.required" => 'لابد من ادخال احتيار القسم ',
            "category_id.exists" => ' القسم المختار غير موجود',
        ];

        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|max:100',
            'name_en' => 'required|max:100',
            'mobile' => 'required|unique:users,mobile',
            'email' => 'required|email|unique:users,email',
            'birth_date' => 'required|date-format:Y-m-d',
            'gender' => 'required|in:1,2',
            'academy_id' => 'required|exists:academies,id',
            'photo' => 'required|mimes:jpg,jpeg,png',
            'password' => 'required|confirmed|min:6',
            'team_id' => 'required|exists:teams,id',
            'category_id' => 'required|exists:categories,id'

        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        DB::beginTransaction();

        $fileName = "";
        if (isset($request->photo) && !empty($request->photo)) {
            $fileName = $this->uploadImage('users', $request->photo);
        }
        $status = $request->has('status') ? 1 : 0;
        User::create(['photo' => $fileName, 'status' => $status] + $request->except('_token'));
        DB::commit();

        notify()->success('تم اضافه الطالب  بنجاح ');
        return redirect()->route('admin.users.all')->with(['success' => 'تم اضافه الطالب  بنجاح ']);
    }

    public function edit($id)
    {
        $data = [];
        $data['user'] = User::findOrFail($id);
        $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();
        $data['categories'] = $data['user']->academy->categories;
        $userCategoryId = $data['user']->category->id;
        $data['teams'] = Team::where('category_id', $userCategoryId)->get();
        return view('admin.users.edit', $data);
    }

    public function update($id, Request $request)
    {

        $user = User::findOrFail($id);
        $messages = [
            'name_ar.required' => 'أسم    الطالب  بالعربي  مطلوب  .',
            'name_en.required' => 'أسم  الطالب  بالانجليزي  مطلوب  .',
            'mobile.required' => 'رقم الهاتف مطلوب ',
            'mobile.unique' => 'رقم الهاتف مسجل لدينا من قبل ',
            'email.required' => 'البريد الالكتروني مطلوب ',
            'email.exists' => 'البريد الالكتروني  مسجل لدينا من قبل  ',
            "password.required" => trans("admin/passwords.passwordRequired"),
            "password.confirmed" => trans("admin/passwords.confirmpassword"),
            "password.min" => trans("admin/passwords.confirmpassword"),
            'academy_id.required' => 'لابد من احتيار الاكاديمية اولا ',
            'academy_id.exists' => 'هذه الاكاديمية غير موجوده ',
            'photo.required' => 'لابد من رفع صوره  الطالب  ',
            'photo.mimes' => ' أمتداد الصوره غير مسموح به ',
            "teams.required" => 'لأابد من أختيار الفرق ',
            "teams.exists" => ' الفريق عير موجود لدينا  ',
            'birth_date.required' => 'تاريخ الميلاد مطلوب',
            'date-format' => ' صيغة التاريخ عير صحيحه لابد من ادخالها Y-m-d',


        ];

        $rules = [
            'name_ar' => 'required|max:100',
            'name_en' => 'required|max:100',
            'mobile' => 'required|unique:users,mobile,' . $user->id,
            'email' => 'required|email|unique:users,email,' . $user->id,
            'birth_date' => 'required|date-format:Y-m-d',
            'academy_id' => 'required|exists:academies,id',
            'photo' => 'sometimes|nullable|mimes:jpg,jpeg,png',
            'team_id' => 'required|exists:teams,id',

        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($request->filled('password')) {
            $rules['password'] = 'required|confirmed|min:6';
        }
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        try {
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request
            if (isset($request->photo) && !empty($request->photo)) {
                $fileName = $this->uploadImage('teams', $request->photo);
                $user->update(['photo' => $fileName]);
            }
            DB::beginTransaction();
            $user->update($request->except('photo'));
            DB::commit();
            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.users.all');
        } catch (\Exception $ex) {
            DB::rollback();
            return abort('404');
        }
    }

    public function getWorkingDay($teamId)
    {
        $team = Team::find($teamId);
        if (!$team) {
            notify()->error('الفريق  غير موجوده لدينا ');
            return redirect()->route('admin.teams.all');
        }

        $time = TeamTime::where('team_id', $teamId)->first();
        return view('admin.teams.workingdays', compact('academies', 'team', 'time'));
    }

    public function saveWorkingDay(Request $request)
    {
        try {
            $messages = [
                'team_id.required' => 'رقم الفرقه مطلوب .',
                'team_id.exists' => ' الفرقه غير موجوده .',
            ];

            $validator = Validator::make($request->all(), [
                'team_id' => 'required|exists:teams,id',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $inputs = ['id', 'saturday_status', 'sunday_status', 'monday_status', 'tuesday_status', 'wednesday_status', 'thursday_status', 'friday_status'];

            $team = Team::find($request->team_id);
            if (!$team) {
                notify()->error('الفريق  غير موجوده لدينا ');
                return redirect()->route('admin.teams.all');
            }
            $times = TeamTime::query();
            $times = $times->where('team_id', $request->team_id);
            if ($times->first()) {
                $teamTime = TeamTime::where('team_id', $request->team_id)->first();   // must load model first then update to let mutators work on update
                $teamTime->update($request->except($inputs + ['_token']));
                $this->setDayStatus($request);
                notify()->success('تم تحديث الاوقات بنجاح ');
                return redirect()->back();
            } else {

                $time = TeamTime::create($request->except($inputs));
                $request->request->team_id = $time;
                $this->setDayStatus($request);
                notify()->success('تم تحديث الاوقات بنجاح ');
                return redirect()->back();
            }
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    protected function setDayStatus(Request $request)
    {
        $status = ['saturday_status', 'sunday_status', 'monday_status', 'tuesday_status', 'wednesday_status', 'thursday_status', 'friday_status'];
        $times = TeamTime::where('team_id', $request->team_id)->first();
        foreach ($status as $st) {
            if ($request->has($st)) {
                $times->update([$st => 1]);
            } else {
                $times->update([$st => 0]);
            }
        }
    }


    public function teams($coachId)
    {
        $coach = Coach::findOrFail($coachId);
        $teams = $coach->teams()->get();
        notify()->success('تم عرض الفرق بنجاح  ');
        return view('admin.coaches.teams', compact('teams', 'coach'));
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        notify()->success('تمت حذف  الاعب بمحتواه');
        return redirect()->route('admin.users.all');
    }


}
