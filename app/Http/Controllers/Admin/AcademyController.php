<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Setting;
use App\Models\Team;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Validator;
use Hash;

class AcademyController extends Controller
{

    use PublicTrait;

    public function index()
    {
        $academies = Academy::get();
        return view('admin.academies.index', compact('academies'));
    }

    public function create()
    {
        $categories = Category::active()->get();
        return view('admin.academies.create', compact('categories'));
    }


    public function store(Request $request)
    {
        try {
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'max' => 'لابد الايزيد عدد اخرف الحقب عن 100 حرف بالمسافات ',
                'category_id.exists' => ' القسم غير موجود ',
                'logo.mimes' => 'لابد من رفع صوره صحيحة الامتداد',
                // "categories.required" => 'لأبد من أختيار  اقسام الاكاديمية ',
                //"categories.array" => 'لأبد من أختيار  اقسام الاكاديمية ',
                //"categories.min" => 'لأبد من أختيار  اقسام الاكاديمية ',
                //"categories.*.required" => 'لأبد من أختيار  اقسام الاكاديمية ',
                //"categories.*.exists" => 'لأبد من أختيار  اقسام الاكاديمية ',
            ];

            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
                'address_ar' => 'required|max:225',
                'address_en' => 'required|max:225',
                //'categories' => 'required|array|min:1',
                //'categories.*' => 'required|exists:categories,id',
                'logo' => 'required|mimes:jpeg,png,bmp,gif,svg',
            ], $messages);


            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $fileName = "";
            if (isset($request->logo) && !empty($request->logo)) {
                $fileName = $this->uploadImage('academies', $request->logo);
            }

            $code = strtolower($this->getRandomCode(4, 'App\Models\Academy'));
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status, 'code' => $code]); //add request
            $academy = Academy::create(['logo' => $fileName] + $request->except('_token', 'logo'));
            /*if ($academy->id) {
                $academy->categories()->attach($request->categories);
            }*/
            notify()->success('تمت الاضافة بنجاح ');
            return redirect()->route('admin.academies.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    public function edit($id)
    {
        $academy = Academy::find($id);
        // $categories = Category::select('id', 'name_ar as name')->get();
        if (!$academy) {
            notify()->success('الأكاديمية غير موجوده لدينا ');
            return redirect()->route('admin.academies.all');
        }

        return view('admin.academies.edit', compact('academy' /*,'categories'*/));
    }

    public function update($id, Request $request)
    {

        $academy = Academy::find($id);
        if (!$academy) {
            notify()->success('الأكاديمية غير موجوده لدينا ');
            return redirect()->route('admin.academies.edit', $id);
        }
        $messages = [
            'required' => 'هذا الحقل مطلوب ',
            'max' => 'لابد الايزيد عدد اخرف الحقب عن 100 حرف بالمسافات ',
            "categories.array" => 'لأبد من أختيار  اقسام الاكاديمية ',
            //"categories.min" => 'لأبد من أختيار  اقسام الاكاديمية ',
            // "categories.*.required" => 'لأبد من أختيار  اقسام الاكاديمية ',
            // "categories.*.exists" => 'لأبد من أختيار  اقسام الاكاديمية ',
            'logo.mimes' => 'لابد من رفع صوره صحيحة الامتداد'
        ];
        $validator = Validator::make($request->all(), [
            'name_ar' => 'required|max:100',
            'name_en' => 'required|max:100',
            'address_ar' => 'required|max:225',
            'address_en' => 'required|max:225',
            //'categories' => 'required|array|min:1',
            //'categories.*' => 'required|exists:categories,id',
            'logo' => 'mimes:jpg,jpeg,png',

        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        if (isset($request->logo) && !empty($request->logo)) {
            $fileName = $this->uploadImage('academies', $request->logo);
            $academy->update(['logo' => $fileName]);
        }

        $status = $request->has('status') ? 1 : 0;
        $request->request->add(['status' => $status]); //add request
        $academy->update($request->except('_token', 'logo'));
        //   $academy->categories()->sync($request->categories);
        notify()->success('تمت التعديل  بنجاح ');
        return redirect()->route('admin.academies.all');

    }

    public function loadAboutUs(Request $request)
    {

        $academy = Academy::findOrfail($request->academy_id);
        $settings = $academy->setting;
        $view = view('admin.aboutus.loadData', compact('settings'))->renderSections();
        return response()->json([
            'content' => $view['main'],
        ]);
    }


    public function academyAboutUs($id)
    {
        $academy = Academy::findOrFail($id);
        $settings = $academy->setting;

        return view('admin.academies.aboutUs', compact('settings', 'academy'));
    }


    public function saveAboutUs(Request $request)
    {
        try {
            $messages = [
                'academy_id.required' => ' لابد من تحديد الاكاديمية ',
                'academy_id.exists' => 'الاكاديمية غير موجوده لدينا ',
                'email.email'  => ' البريد الالكتروني عبر صحيح',
            ];

            $validator = Validator::make($request->all(), [
                'academy_id' => 'required|exists:academies,id',
                'email' => 'sometimes|nullable|email',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            $academy = Academy::findorFail($request->academy_id);
            $settings = $academy->setting;

            if ($settings === null) {
                $setting = new Setting($request->all());
                $academy->setting()->save($setting);
                notify()->success('تمت الحفظ بنجاح ');
                return redirect()->route('admin.academies.all');

            } else {
                $academy->setting->update($request->all());
            }
            notify()->success('تمت التعديل بنجاح ');
            return redirect()->route('admin.academies.all');

        } catch (\Exception $ex) {
            return abort('404');
        }
    }

    public function deleteAcademy($id)
    {
        $academy = Academy::findOrFail($id);
        $academy->delete();
        notify()->success('تمت حذف الاكاديمية بكل محتواها بنجاح ');
        return redirect()->route('admin.academies.all');
    }
}
