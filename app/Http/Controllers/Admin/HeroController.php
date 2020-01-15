<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Hero;
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

class HeroController extends Controller
{
    use PublicTrait;

    public function index()
    {
        $heroes = Hero::get();
        return view('admin.heroes.index', compact('heroes'));
    }

    public function currentWeek()
    {
        $data = [];
        $header = "أبطال الاسبوع ";
        $weekStartEnd = currentWeekStartEndDate();
        $startWeek = date('Y-m-d', strtotime($weekStartEnd['startWeek']));
        $endWeek = date('Y-m-d', strtotime($weekStartEnd['endWeek']));

        $data['heroes'] = Hero::whereBetween('created_at', [$startWeek, $endWeek])->get();
        $data['header'] = $header;
        $data['startWeek'] = $startWeek;
        $data['endWeek'] = $endWeek;
        return view('admin.heroes.index', $data);
    }

    public function create()
    {
        $data['teams'] = Team::with(['users' => function ($q) {
            $q->active()
                ->subscribed()
                ->select('id', 'name_' . app()->getLocale() . ' as name', 'photo', 'team_id');
        }])
            ->active()
            ->select('id', 'name_' . app()->getLocale() . ' as name', 'photo')
            ->whereHas('users', function ($qq) {
                $qq->active()
                    ->subscribed();
            })->get();

        $weekStartEnd = currentWeekStartEndDate();
        $data['startWeek'] = $weekStartEnd['startWeek'];
        $data['endWeek'] = $weekStartEnd['endWeek'];
        return view('admin.heroes.create', $data);
    }


    public function store(Request $request)
    {
        $messages = [
            'studentIds.required' => '  لابد من أختيار طلاب  .',
            'studentIds.array' => '  لابد من أختيار طلاب  .',
            'studentIds.min' => '  لابد من أختيار طلاب  .',
            'studentIds.*.required' => '  لابد من أختيار طلاب  .',
            'studentIds.*.numeric' => '  لابد من أختيار طلاب  .',
        ];

        $validator = Validator::make($request->all(), [
            'studentIds' => 'required|array|min:1',
            'studentIds.*' => 'required|numeric',
            'startWeek' => 'required',
            'endWeek' => 'required'

        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $students = $request->studentIds;

        $startWeek = date('Y-m-d', strtotime($request->startWeek));
        $endWeek = date('Y-m-d', strtotime($request->endWeek));

        if (count($students) > 0) {
            foreach ($students as $student) {
                $studentAlreadyHeroOfCurrentWeek = Hero::where('user_id', $student)->whereBetween('created_at', [$startWeek, $endWeek])->first();
                if (!$studentAlreadyHeroOfCurrentWeek) {
                    Hero::insert(['user_id' => $student]);
                }
            }
            notify()->success('تمت  الاضافة بنجاح ');
            return redirect()->route('admin.heroes.all')->with(['success' => 'تم اضافه أبطال هذا الاسبوع بنجاح ']);
        }
        notify()->success(' فشلت عملببة الاضافة ');
        return redirect()->route('admin.heroes.all')->with(['error' => 'فشلت عمليه الحفظ الرجاء المحاولة مجداا ']);
    }


    public function delete($eventId)
    {
    }
}
