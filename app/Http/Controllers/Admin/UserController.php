<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\AcadSubscription;
use App\Models\Attendance;
use App\Models\Coach;
use App\Models\Subscription;
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
        try {
            $users = User::selection()->orderBy('id','DESC') ->get();
            return view('admin.users.index', compact('users'));
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function view($id){
        $data = [];
        $data['user'] = User::findOrFail($id);
        $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();
        $data['categories'] = $data['user']->academy->categories;
        $userCategoryId = $data['user']->category->id;
        $data['teams'] = Team::where('category_id', $userCategoryId)->get();
        return view('admin.users.view', $data);
    }

    public function create()
    {
        try {
            $academies = Academy::active()->select('id', 'name_ar as name')->get();
            $teams = Team::active()->whereHas('times')->selection()->get();
            return view('admin.users.create', compact('academies', 'teams'));
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function store(Request $request)
    {

        try {
            $messages = [
                'name_ar.required' => 'أسم الاعب  بالعربي  مطلوب  .',
                'name_en.required' => 'أسم  الاعب  بالانجليزي  مطلوب  .',
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
                'photo.required' => 'لابد من رفع صوره  الاعب  ',
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
                'photo' => 'required|mimes:jpeg,jpg,png,bmp,gif,svg',
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
            $user = User::create(['photo' => $fileName, 'status' => $status] + $request->except('_token'));
            DB::commit();

            notify()->success('تم اضافه الاعب  بنجاح برجاء اضافه اشتراك الاكاديمية ');
            return redirect()->route('admin.academy.create.subscriptions',$user -> id)->with(['success' => 'تم اضافه الاعب  بنجاح برجاء اضافه اشتراك الاكاديمية ']);
        } catch (\Exception $ex) {
            return abort('404');
        }
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
            'name_ar.required' => 'أسم    الاعب  بالعربي  مطلوب  .',
            'name_en.required' => 'أسم  الاعب  بالانجليزي  مطلوب  .',
            'mobile.required' => 'رقم الهاتف مطلوب ',
            'mobile.unique' => 'رقم الهاتف مسجل لدينا من قبل ',
            'email.required' => 'البريد الالكتروني مطلوب ',
            'email.exists' => 'البريد الالكتروني  مسجل لدينا من قبل  ',
            "password.required" => trans("admin/passwords.passwordRequired"),
            "password.confirmed" => trans("admin/passwords.confirmpassword"),
            "password.min" => trans("admin/passwords.confirmpassword"),
            'academy_id.required' => 'لابد من احتيار الاكاديمية اولا ',
            'academy_id.exists' => 'هذه الاكاديمية غير موجوده ',
            'photo.required' => 'لابد من رفع صوره  الاعب  ',
            'photo.mimes' => ' أمتداد الصوره غير مسموح به ',
            "team_id.required" => 'لأابد من أختيار الفرق ',
            "team_id.exists" => ' الفريق عير موجود لدينا  ',
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
            'photo' => 'sometimes|nullable|mimes:jpeg,jpg,png,bmp,gif,svg',
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


        if ($user->academysubscribed == 1) {  // if there is current subscription cannot move user from team to another
            if ($user->team_id != $request->team_id or $user->category_id != $request->category_id or $user->academy_id != $request->academy_id) {
                notify()->error('لا يمكن تغيير فرقه الاعب بوجود اشتراك حالي مفعل  ');
                return redirect()->back()->withErrors(['team_id' => 'لابمكن تغيير فريق الاعب حيث ان هناك اشتراك حالي لم ينتهي بعد  '])->withInput($request->all());
            }
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
        try {
            $coach = Coach::findOrFail($coachId);
            $teams = $coach->teams()->get();
            notify()->success('تم عرض الفرق بنجاح  ');
            return view('admin.coaches.teams', compact('teams', 'coach'));
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();
            notify()->success('تمت حذف  الاعب بمحتواه');
            return redirect()->route('admin.users.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function attendUser(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'userId' => 'required|exists:users,id',
                'attend' => 'required|in:0,1',
                'date' => 'required|date-format:Y-m-d'
            ]);

            if ($validator->fails()) {
                return response()->json(['msg' => $validator -> errors() -> first()], '422');
            }
            $user = User::find($request->userId);
            if (!$user) {
                return response()->json(['status'=> false,'msg' => 'الاعب غير موجود ']);
            }

            $userAlreadyTakeAttendanceToday = Attendance::where([
                ['user_id', $request->userId],
                ['team_id', $user->team->id],
                ['date', $request->date],
            ])->first();

            if ($userAlreadyTakeAttendanceToday) {
                $userAlreadyTakeAttendanceToday->update(['attend' => $request->attend]);
            } else {

                 $currentSubscription = AcadSubscription::current()
                    ->where('user_id', $request->userId)
                    ->whereDate('start_date', '<=', $request->date)
                    ->whereDate('end_date', '>=', $request->date)
                    ->select('id')
                    ->first();  //we allow only one subscription

                    if(!$currentSubscription)
                         return response()->json(['status'=> false,'msg' => "اليوم ليس من ايام الفرقه"]);

                $date = date('Y-m-d', strtotime($request->date));
                $attendance = new Attendance();
                $attendance->user_id = $request->userId;
                $attendance->team_id = $user->team->id;
                $attendance->attend = $request->attend;
                $attendance->subscription_id = $currentSubscription ? $currentSubscription->id : null;
                $attendance->date = $date;
                $user->attendances()->save($attendance);

                return response()->json(['status'=> true,'msg' => "تمت العمليه بنجاح"]);
            }
        } catch (\Exception $ex) {
            return response()->json(['msg'=>$ex], 500);
        }
    }


    public function attendAll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'team_id' => 'required|exists:teams,id',
            'attend' => 'required|in:0,1',
            'date' => 'required|date-format:Y-m-d'
        ]);

        if ($validator->fails()) {
            return response()->json([], '422');
        }
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json([], '500');
        }
        $date = $request->date;
        $teamId = $request->team_id;
        $attend = $request->attend;

        $users = User::active()->with(['attendances' => function ($q) use ($date) {
            $q->whereDate('date', '=', $date);
        }])->whereHas('team', function ($q) use ($teamId) {
            $q->where('id', $teamId);
        })->get();

        if (isset($users) && $users->count() > 0) {
            foreach ($users as $user) {
                if (isset($user->attendances) && $user->attendances->count() > 0) {
                    //update user attendance
                    Attendance::where([
                        ['date', $date],
                        ['team_id', $teamId],
                        ['user_id', $user->id]
                    ])->update(['attend' => $attend]);

                } else {
                    $currentSubscription = AcadSubscription::current()->where('user_id', $request->userId)->select('id')->first();  //we allow only one subscription

                    //create user attendance
                    $attendance = new Attendance();
                    $attendance->user_id = $user->id;
                    $attendance->team_id = $teamId;
                    $attendance->attend = $attend;
                    $attendance->subscription_id = $currentSubscription ? $currentSubscription->id : null;
                    $attendance->date = date('Y-m-d', strtotime($date));
                    $user->attendances()->save($attendance);
                }
            }
        }

        return response()->json(['attend' => $attend], 200);

    }

}
