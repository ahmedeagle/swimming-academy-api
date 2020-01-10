<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Validator;
use Hash;

class AcademyController extends Controller
{

    public function index()
    {
        $academies = Academy::get();
        return view('admin.academies.index', compact('academies'));
    }

    public function create()
    {
        return view('admin.academies.create');
    }


    public function store(Request $request)
    {
        try {
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'max' => 'لابد الايزيد عدد اخرف الحقب عن 100 حرف بالمسافات ',
            ];

            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
                'address_ar' => 'required|max:225',
                'address_en' => 'required|max:225',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request
            Academy::create($request->except('_token'));
            notify()->success('تمت الاضافة بنجاح ');
            return redirect()->route('admin.academies.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }


    public function edit($id)
    {

        $academy = Academy::find($id);

        if (!$academy) {
            notify()->success('الأكاديمية غير موجوده لدينا ');
            return redirect()->route('admin.academies.all');
        }

        return view('admin.academies.edit', compact('academy'));
    }

    public function update($id, Request $request)
    {
        try {
            $academy = Academy::find($id);
            if (!$academy) {
                notify()->success('الأكاديمية غير موجوده لدينا ');
                return redirect()->route('admin.academies.edit', $id);
            }
            $messages = [
                'required' => 'هذا الحقل مطلوب ',
                'max' => 'لابد الايزيد عدد اخرف الحقب عن 100 حرف بالمسافات ',
            ];
            $validator = Validator::make($request->all(), [
                'name_ar' => 'required|max:100',
                'name_en' => 'required|max:100',
                'address_ar' => 'required|max:225',
                'address_en' => 'required|max:225',
            ], $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }
            $status = $request->has('status') ? 1 : 0;
            $request->request->add(['status' => $status]); //add request
            Academy::where('id', $id)->update($request->except('_token'));
            notify()->success('تمت التعديل  بنجاح ');
            return redirect()->route('admin.academies.all');
        } catch (\Exception $ex) {
            return abort('404');
        }
    }
}
