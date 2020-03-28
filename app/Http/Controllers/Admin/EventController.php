<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Coach;
use App\Models\Event;
use App\Models\Image;
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

use File;

class EventController extends Controller
{
    use PublicTrait;

    public function index()
    {
        $events = Event::orderBy('id', 'DESC')->get();
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
        $filename = $this->uploadImage('events', $file);

        return response()->json([
            'name' => $filename,
            'original_name' => $file->getClientOriginalName(),
        ]);

    }

    public function store(Request $request)
    {

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
        $event = Event::create(['photo' => $fileName, 'status' => $status] + $request->except('_token'));

        // save dropzone images
        if ($request->has('document') && count($request->document) > 0) {
            foreach ($request->document as $image) {
                Image::create([
                    'imageable_id' => $event->id,
                    'imageable_type' => 'App\Models\Event',
                    'photo' => $image,
                ]);
            }
        }

        DB::commit();

        //send push notification to user in this category
        $devices_tokens = User::subScribed()->whereNotNull('device_token')->where('device_token', '!=', '')->where('category_id', $request->category_id)->pluck('device_token')->toArray();
        if (count($devices_tokens) > 0)
            (new \App\Http\Controllers\PushNotificationController(['title' => 'اضافه فاعليه للاكاديمية ', 'body' => $request->title_ar]))->sendMulti($devices_tokens);

        notify()->success('تم اضافه الفاعلية  بنجاح ');
        return redirect()->route('admin.events.all')->with(['success' => 'تم اضافه  الفاعلية   بنجاح ']);
    }

    public function edit($id)
    {

        $data = [];
        $data['academies'] = Academy::active()->get();
        $data['event'] = Event::with(['images' => function ($q) {
            $q->select('id', 'imageable_id', 'imageable_type', DB::raw('photo as photo'));
        }])->findOrFail($id);

        $file_list = array();
        if (isset($data['event']->images) && count($data['event']->images) > 0) {
            foreach ($data['event']->images as $image) {
                $imageStr = substr($image->photo, strpos($image->photo, "images"));
                $size = File::size(base_path($imageStr));
                $name = File::basename(base_path($imageStr));
                $file_list[] = array('name' => $name, 'size' => $size, 'path' => $imageStr, 'allPath' => $image->photo);
            }
        }
        $data['event']->images = $file_list;

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

            //delete previous images
            Image::where('imageable_id', $event->id)->where('imageable_type', 'App\Models\Event')->delete();
            // save dropzone images
            if ($request->has('document') && count($request->document) > 0) {
                //insert new images
                foreach ($request->document as $image) {
                    Image::create([
                        'imageable_id' => $event->id,
                        'imageable_type' => 'App\Models\Event',
                        'photo' => $image,
                    ]);
                }
            }

            DB::commit();
            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.events.all');
        } catch (\Exception $ex) {
            DB::rollback();
            return $ex->getMessage();
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
