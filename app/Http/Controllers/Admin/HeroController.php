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
use Session;

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
        $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();
        $data['teams'] = Team::with(['users' => function ($q) {
            $q->active()
                ->select('id', 'name_' . app()->getLocale() . ' as name', 'photo', 'team_id');
        }])
            ->active()
            ->select('id', 'name_' . app()->getLocale() . ' as name', 'photo')
            ->whereHas('users', function ($qq) {
                $qq->active();
            })->get();

        $weekStartEnd = currentWeekStartEndDate();
        $data['startWeek'] = $weekStartEnd['startWeek'];
        $data['endWeek'] = $weekStartEnd['endWeek'];
        return view('admin.heroes.create', $data);
    }


    public function store(Request $request)
    {
        $messages = [
            'studentIds.required' => '  لابد من أختيار لاعبين  .',
            'studentIds.array' => '  لابد من أختيار لاعبين  .',
            'studentIds.min' => '  لابد من أختيار لاعبين  .',
            'studentIds.*.required' => '  لابد من أختيار لاعبين  .',
            'studentIds.*.numeric' => '  لابد من أختيار لاعبين  .',
            'category_id.required' => 'لابد من احتيار القسم  اولا ',
            'category_id.exists' => 'هذه القسم  غير موجوده ',
            'academy_id.required' => 'لابد من احتيار الاكاديمية اولا ',
            'academy_id.exists' => 'هذه الاكاديمية غير موجوده ',
        ];

        $validator = Validator::make($request->all(), [
            'studentIds' => 'required|array|min:1',
            'studentIds.*' => 'required|numeric',
            'startWeek' => 'required',
            'endWeek' => 'required',
            'academy_id' => 'required|exists:academies,id',
            'category_id' => 'required|exists:categories,id',


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
                    Hero::insert([
                        'user_id' => $student,
                        'team_id' => $request->team_id,
                        'category_id' => $request->category_id
                    ]);
                }
            }
            notify()->success('تمت  الاضافة بنجاح ');
            return redirect()->route('admin.heroes.all')->with(['success' => 'تم اضافه أبطال هذا الاسبوع بنجاح ']);
        }
        notify()->error(' فشلت عملبة الاضافة ');
        return redirect()->route('admin.heroes.all')->with(['error' => 'فشلت عمليه الحفظ الرجاء المحاولة مجداا ']);
    }


    public function addHeroNote(Request $request)
    {

        try {
            $messages = [
                'note_ar.required' => '  النبذه بالعربي  مطلوبه  .',
                'note_en.required' => '  النبذه بالانجليزية مطلوب .',
                'heroId.required' => '  رقم الاعب مطلوب  .',
                'heroId.exists' => 'رقم الاعب غير موجود  ',
            ];

            $validator = Validator::make($request->all(), [
                'note_ar' => 'required',
                'note_en' => 'required',
                'heroId' => 'required|exists:heroes,id',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with(['modalId' => $request->heroId]);
            }
            $hero = Hero::find($request->heroId);
            $hero->makeVisible(['note_ar', 'note_en']);

            $hero->update([
                'note_ar' => $request->note_ar,
                'note_en' => $request->note_en
            ]);

            notify()->success('تم التحديث بنجاح ');

            return redirect()->route('admin.heroes.all')->with(['success' => 'تم التحديث بنجاح ']);

        } catch (\Exception $ex) {
            return abort('404');
        }

    }

    public function delete($heroId)
    {
        $hero = Hero::findOrFail($heroId);
        $hero->delete();

        notify()->success(' تم الحذف بنجاح ');
        return redirect()->back();
    }
}
