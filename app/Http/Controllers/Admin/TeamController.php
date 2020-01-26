<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Coach;
use App\Models\Team;
use App\Models\TeamTime;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Namshi\JOSE\Signer\SecLib\RS384;
use Validator;
use Hash;

class TeamController extends Controller
{

    use PublicTrait;

    public function index()
    {
        try {
            $teams = Team::selection()->get();
            return view('admin.teams.index', compact('teams'));
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function create()
    {
        try {
            $academies = Academy::active()->select('id', 'name_ar as name')->get();
            $coaches = Coach::active()->select('id', 'name_ar as name')->get();
            return view('admin.teams.create', compact('academies', 'coaches'));

        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    public function store(Request $request)
    {

        try {
            $messages = [
                'name_ar.required' => 'أسم  الفريق بالعربي  مطلوب  .',
                'name_en.required' => 'أسم  الفريق بالانجليزي  مطلوب  .',
                'max' => 'لايد الا يتجاوز عدد حروف الحقل 100 حرف ',
                'quotas.required' => ' لابد من ادحال عدد الحصص الشهرية للتدريب بالفرقه  .',
                'quotas.numeric' => ' عدد حصص التدريب لابد ان تكون أرقام ',
                'category_id.required' => 'لابد من احتيار القسم اولا ',
                'category_id.exists' => 'هذه القسم غير موجوده ',
                'photo.required' => 'لابد من رفع صوره للفريق ',
                'photo.mimes' => ' أمتداد الصوره غير مسموح به ',
                'coach_id.required' => 'لابد من تحديد كابتن للفريق',
                'coach_id.exists' => 'الكابتن غير موجود لدينا',
            ];

            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
                'quotas' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'coach_id' => 'required|exists:coahes,id',
                'photo' => 'required|mimes:jpg,jpeg,png'
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $fileName = "";
            if (isset($request->photo) && !empty($request->photo)) {
                $fileName = $this->uploadImage('teams', $request->photo);
            }
            $status = $request->has('status') ? 1 : 0;
            Team::create(['photo' => $fileName, 'status' => $status] + $request->except('_token'));
            notify()->success('تمت الاضافة بنجاح ');
            return redirect()->route('admin.teams.all');
        } catch (\Exception $ex) {
            return abort('404');
        }

    }

    public function edit($id)
    {

        try {
            $data['team'] = Team::selection()->find($id);
            $teamAcademy = $data['team']->category->academy;
            $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();
            $data['categories'] = $teamAcademy->categories;
            $data['coaches'] = Coach::active()->select('id', 'name_ar as name')->get();
            $coaches = Coach::active()->select('id', 'name_ar as name')->get();

            if (!$data['team']) {
                notify()->error(' الفريق  غير موجوده لدينا ');
                return redirect()->route('admin.teams.all');
            }

            return view('admin.teams.edit', $data);
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function update($id, Request $request)
    {

        try {
            $team = Team::find($id);
            if (!$team) {
                notify()->error('الفريق  غير موجوده لدينا ');
                return redirect()->route('admin.teams.edit', $id);
            }

            $messages = [
                'name_ar.required' => 'أسم  الفريق بالعربي  مطلوب  .',
                'name_en.required' => 'أسم  الفريق بالانجليزي  مطلوب  .',
                'max' => 'لايد الا يتجاوز عدد حروف الحقل 100 حرف ',
                'quotas.required' => ' لابد من ادحال عدد الحصص الشهرية للتدريب بالفرقه  .',
                'quotas.numeric' => ' عدد حصص التدريب لابد ان تكون أرقام ',
                'category_id.required' => 'لابد من احتيار القسم  اولا ',
                'category_id.exists' => 'هذه القسم  غير موجوده ',
                'photo.required' => 'لابد من رفع صوره للفريق ',
                'photo.mimes' => ' أمتداد الصوره غير مسموح به ',
                'coach_id.required' => 'لابد من تحديد كابتن للفريق',
                'coach_id.exists' => 'الكابتن غير موجود لدينا',
            ];

            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
                'quotas' => 'required|numeric',
                'category_id' => 'required|exists:categories,id',
                'photo' => 'sometimes|nullable|mimes:jpg,jpeg,png',
                'coach_id' => 'required|exists:coahes,id'

            ], $messages);

            if ($validator->fails()) {
                return $validator->errors();
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request

            if (isset($request->photo) && !empty($request->photo)) {
                $fileName = $this->uploadImage('teams', $request->photo);
                $team->update(['photo' => $fileName]);
            }
            $team->update($request->except('photo'));

            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.teams.all');
        } catch (\Exception $ex) {
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


    public function getTeamCoaches($teamId)
    {
        $data['team'] = Team::findOrFail($teamId);
        $data['coaches'] = $data['team']->coaches;
        return view('admin.teams.coaches', $data);
    }

    public function getTeamStudents($teamId)
    {
        $data['team'] = Team::findOrFail($teamId);
        $data['users'] = $data['team']->users;
        return view('admin.teams.users', $data);
    }


    public function loadHeroes(Request $request)
    {
        $team = Team::find($request->team_id);
        if (!$team) {
            return response()->json(['content' => null]);
        }
        $users = $team->users;
        $view = view('admin.teams.heroes', compact('users'))->with('message', ' عفوا لايوجد اي لاعبين في هذا الفريق فضلا قم باضافه لاعبين للفريق او اختر فريق اخر ثم المحاوله مجددا ')->renderSections();
        return response()->json([
            'content' => $view['main'],
        ]);
    }

    public function deleteTeam($id)
    {
        $team = Team::findOrFail($id);
        $team->delete();
        notify()->success('تمت حذف الفريق بمستخدميه بنجاح ');
        return redirect()->route('admin.teams.all');
    }


}
