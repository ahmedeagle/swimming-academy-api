<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Admin;
use DB;
use Auth;
use Validator;
use Hash;

class DashboardController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard.index');
    }

    public function aboutUs()
    {
        $settings = Setting::first();
        $academies = Academy::active() -> get();
        return view('admin.aboutus.edit', compact('settings','academies'));
    }

    public function saveAboutUs(Request $request)
    {
        try {
            $messages = [
                'academy_id.required' => ' لابد من تحديد الاكاديمية ',
                'academy_id.exists' => 'الاكاديمية غير موجوده لدينا '
            ];

            $validator = Validator::make($request->all(), [
                'academy_id' => 'required|exists:academies,id'
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }


            $academy = Academy::findorFail($request -> academy_id);
             $settings = $academy -> setting;
            if ($settings === null) {
                $setting = new Setting($request->all());
                $academy->setting()->save($setting);
            } else {
                $academy->setting->update($request->all());
            }
            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.aboutus');

        } catch (\Exception $ex) {
            return abort('404');
        }
    }

}
