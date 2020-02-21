<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Champion;
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
use mysql_xdevapi\Exception;
use Namshi\JOSE\Signer\SecLib\RS384;
use Validator;
use Hash;
use Session;

class ChampionController extends Controller
{
    use PublicTrait;

    public function index()
    {
        try {
            return   $champions = Champion::get();
            return view('admin.champions.index', compact('champions'));
        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function create()
    {
        try {
            $data['academies'] = Academy::active()->select('id', 'name_ar as name')->get();
            $data['categories'] = Category::with(['allUsers' => function ($q) {
                $q->active() -> academySubScribed()

                    ->select('users.id', 'users.name_' . app()->getLocale() . ' as name', 'users.photo', 'users.category_id');
            }])
                ->active()
                ->select('categories.id', 'categories.name_' . app()->getLocale() . ' as name')
                ->whereHas('allUsers', function ($qq) {
                    $qq->active()
                        ;
                })->get();
            return view('admin.champions.create', $data);
        }catch (\Exception $ex){
            return abort('404');
        }
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
            'name_ar.required' => 'اسم المسابقه بالعربي مطلوب ',
            'name_en.required' => 'اسم المسابقه بالانجليزي مطلوب ',
            'name_ar.max' => 'اسم المسابقه بالعربي لابد الا يتجاوز ال 100 حرف بالمسافات ',
            'name_en.max' => 'اسم المسابقه بالانجليزي  لابد الا يتجاوز ال 100 حرف بالمسافات ',

        ];

        $validator = Validator::make($request->all(), [
            'studentIds' => 'required|array|min:1',
            'studentIds.*' => 'required|numeric',
            'academy_id' => 'required|exists:academies,id',
            'category_id' => 'required|exists:categories,id',
            'name_ar' => 'required|max:100',
            'name_en' => 'required|max:100',

        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $students = $request->studentIds;

        if (count($students) > 0) {
            foreach ($students as $student) {
                Champion::insert([
                    'user_id' => $student,
                    'category_id' => $request->category_id,
                    'name_ar' => $request->name_ar,
                    'name_en' => $request->name_en,
                ]);
            }
            notify()->success('تمت  الاضافة بنجاح ');
            return redirect()->route('admin.champions.all')->with(['success' => 'تمت الاضافة بنجاح ']);
        }
        notify()->error(' فشلت عملبة الاضافة ');
        return redirect()->route('admin.champions.all')->with(['error' => 'فشلت عمليه الحفظ الرجاء المحاولة مجداا ']);
    }


    public function addChampionNote(Request $request)
    {

        try {
            $messages = [
                'note_ar.required' => '  النبذه بالعربي  مطلوبه  .',
                'note_en.required' => '  النبذه بالانجليزية مطلوب .',
                'championId.required' => '  رقم الاعب مطلوب  .',
                'championId.exists' => 'رقم الاعب غير موجود  ',
            ];

            $validator = Validator::make($request->all(), [
                'note_ar' => 'required',
                'note_en' => 'required',
                'championId' => 'required|exists:champions,id',
                'hero_photo' => 'sometimes|nullable|mimes:jpeg,jpg,png,bmp,gif,svg',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all())->with(['modalId' => $request->championId]);
            }
            $champion = Champion::find($request->championId);
            $champion->makeVisible(['note_ar', 'note_en']);

            if (isset($request->champion_photo) && !empty($request->champion_photo)) {
                $fileName = $this->uploadImage('users', $request->champion_photo);
                $champion->update(['champion_photo' => $fileName]);
            }

            $champion->update([
                'note_ar' => $request->note_ar,
                'note_en' => $request->note_en
            ]);

            notify()->success('تم التحديث بنجاح ');
            return redirect()->route('admin.champions.all')->with(['success' => 'تم التحديث بنجاح ']);
        } catch (\Exception $ex) {
            return abort('404');
        }

    }

    public function delete($championId)
    {
        $championId = Champion::findOrFail($championId);
        $championId->delete();

        notify()->success(' تم الحذف بنجاح ');
        return redirect()->back();
    }
}
