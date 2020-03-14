<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Activity;

use App\Models\Team;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use Validator;

class ActivityController extends Controller
{
    use PublicTrait;

    public function index()
    {
       return  $activities = Activity::orderBy('id','DESC') -> get();
        return view('admin.activities.index', compact('activities'));
    }

    public function create()
    {
        $academies = Academy::active()->get();
        return view('admin.activities.create', compact('academies'));
    }


    public function store(Request $request)
    {
        try {
            $messages = [
                'title_ar.required' => ' العنوان   بالعربي  مطلوب  .',
                'title_en.required' => ' العنوان   بالانجليزي  مطلوب  .',
                'videoLink.required' => ' لابج من ادخال رابط الفيديو   .',
                'academy_id.required' => 'لابد من أختيار الاكاديمية ',
                'academy_id.exists' => 'الأكاديمية غير موجوده لدينا',
                'category_id.required' => 'لابد من أختيار القسم  ',
                'category_id.exists' => 'القسم  غير موجوده لدينا',
                'team_id.required' => 'لابد من أختيار فرقه اولا',
                'team_id.exists' => 'الفرقه غير موجوده لدينا',
            ];

            $validator = Validator::make($request->all(), [
                'title_ar' => 'required|max:100',
                'title_en' => 'required|max:100',
                'videoLink' => 'required|max:225',
                'academy_id' => 'required|exists:academies,id',
                'category_id' => 'required|exists:categories,id',
                'team_id' => 'required|exists:teams,id'


            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            DB::beginTransaction();
            $status = $request->has('status') ? 1 : 0;
            Activity::create(['status' => $status] + $request->except('_token'));
            DB::commit();
            notify()->success('تم اضافه النشاط بنجاح ');
            return redirect()->route('admin.activities.all')->with(['success' => 'تم اضافه النشاط بنجاح ']);
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex;//abort('404');
        }
    }

    public
    function edit($id)
    {
        $data = [];
        $data['academies'] = Academy::active()->get();
        $data['activity'] = Activity::findOrFail($id);
        $data['categories'] = $data['activity']->academy->categories;
        $data['teams'] = Team::where('category_id', $data['activity']->category->id)->get();
        return view('admin.activities.edit', $data);
    }

    public
    function update($id, Request $request)
    {

        $activity = Activity::findOrFail($id);
        $messages = [
            'title_ar.required' => ' العنوان   بالعربي  مطلوب  .',
            'title_en.required' => ' العنوان   بالانجليزي  مطلوب  .',
            'videoLink.required' => ' لابج من ادخال رابط الفيديو   .',
            'academy_id.required' => 'لابد من أختيار الاكاديمية ',
            'academy_id.exists' => 'الأكاديمية غير موجوده لدينا',
            'category_id.required' => 'لابد من أختيار القسم  ',
            'category_id.exists' => 'القسم  غير موجوده لدينا',
        ];

        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|max:100',
            'title_en' => 'required|max:100',
            'videoLink' => 'required|max:225',
            'academy_id' => 'required|exists:academies,id',
            'category_id' => 'required|exists:categories,id'
        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }


        try {
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request
            DB::beginTransaction();
            $activity->update($request->all());
            DB::commit();
            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.activities.all');
        } catch (\Exception $ex) {
            DB::rollback();
            return abort('404');
        }
    }

    public
    function delete($eventId)
    {
        $activity = Activity::findOrFail($eventId);
        $activity->delete();
        notify()->success('تمت  الحذف  بنجاح ');
        return redirect()->route('admin.activities.all')->with(['success' => '  تمت  الحذف  بنجاح ']);
    }
}
