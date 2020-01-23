<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Coach;
use App\Models\Event;
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

class EventController extends Controller
{
    use PublicTrait;

    public function index()
    {
        $events = Event::get();
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        $academies = Academy::active()->get();
        return view('admin.events.create', compact('academies'));
    }


    public function storeEventImages(Request $request)
    {

        $file = $request->file('dzfile');
        $filename = $this->uploadImage('events',$file);

        return response()->json([
            'name' => $filename,
            'original_name' => $file->getClientOriginalName(),
        ]);

    }

    public function store(Request $request)
    {
        return $request;

        $messages = [
            'title_ar.required' => ' العنوان   بالعربي  مطلوب  .',
            'title_en.required' => ' العنوان   بالانجليزي  مطلوب  .',
            'description_ar.required' => '  المحتوي   بالعربي  مطلوب  .',
            'description_en.required' => ' المحتوي   بالانجليزي  مطلوب  .',
            'photo.required' => 'لابد من رفع صوره اولا ',
            'photo.mimes' => 'امتداد صوره غير مسموح به',
            'academy_id.required' => 'لابد من أختيار الاكاديمية ',
            'academy_id.exists' => 'الأكاديمية غير موجوده لدينا',
            'category_id.required' => 'لابد من أختيار القسم  ',
            'category_id.exists' => 'القسم  غير موجوده لدينا',
        ];

        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|max:100',
            'title_en' => 'required|max:100',
            'description_ar' => 'required',
            'description_en' => 'required',
            'photo' => 'required|mimes:jpg,jpeg,png',
            'academy_id' => 'required|exists:academies,id',
            'category_id' => 'required|exists:categories,id'
        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        DB::beginTransaction();

        $fileName = "";
        if (isset($request->photo) && !empty($request->photo)) {
            $fileName = $this->uploadImage('events', $request->photo);
        }
        $status = $request->has('status') ? 1 : 0;
        Event::create(['photo' => $fileName, 'status' => $status] + $request->except('_token'));
        DB::commit();

        notify()->success('تم اضافه الفاعلية  بنجاح ');
        return redirect()->route('admin.events.all')->with(['success' => 'تم اضافه  الفاعلية   بنجاح ']);
    }

    public function edit($id)
    {
        $data = [];
        $data['academies'] = Academy::active()->get();
        $data['event'] = Event::findOrFail($id);
        $data['categories'] = $data['event']->academy->categories;

        return view('admin.events.edit', $data);
    }

    public function update($id, Request $request)
    {

        $event = Event::findOrFail($id);
        $messages = [
            'title_ar.required' => ' العنوان   بالعربي  مطلوب  .',
            'title_en.required' => ' العنوان   بالانجليزي  مطلوب  .',
            'description_ar.required' => '  المحتوي   بالعربي  مطلوب  .',
            'description_en.required' => ' المحتوي   بالانجليزي  مطلوب  .',
            'photo.required' => 'لابد من رفع صوره اولا ',
            'photo.mimes' => 'امتداد صوره غير مسموح به',
            'academy_id.required' => 'لابد من أختيار الاكاديمية ',
            'academy_id.exists' => 'الأكاديمية غير موجوده لدينا',
            'category_id.required' => 'لابد من أختيار القسم  ',
            'category_id.exists' => 'القسم  غير موجوده لدينا',
        ];

        $validator = Validator::make($request->all(), [
            'title_ar' => 'required|max:100',
            'title_en' => 'required|max:100',
            'description_ar' => 'required',
            'description_en' => 'required',
            'photo' => 'sometimes|nullable|mimes:jpg,jpeg,png',
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
            if (isset($request->photo) && !empty($request->photo)) {
                $fileName = $this->uploadImage('events', $request->photo);
                $event->update(['photo' => $fileName]);
            }
            DB::beginTransaction();
            $event->update($request->except('photo'));
            DB::commit();
            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.events.all');
        } catch (\Exception $ex) {
            DB::rollback();
            return abort('404');
        }
    }

    public function delete($eventId)
    {
        $event = Event::findOrFail($eventId);
        $event->delete();
        notify()->success('تمت  الحذف  بنجاح ');
        return redirect()->route('admin.events.all')->with(['success' => '  تمت  الحذف  بنجاح ']);
    }
}
