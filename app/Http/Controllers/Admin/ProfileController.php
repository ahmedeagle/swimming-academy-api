<?php

namespace App\Http\Controllers\Admin;

use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use mysql_xdevapi\Exception;
use Validator;
use Hash;

class ProfileController extends Controller
{
    use PublicTrait;

    public function get_login()
    {
        return view("admin.auth.login");
    }

    public function edit(Request $request)
    {
        $admin = auth('admin')->user();
        return view('admin.profile.index', compact('admin'));
    }

    public function update(Request $request)
    {
        try {
            $admin = auth('admin')->user();
            $messages = [
                'name.required' => ' الاسم مطلوب',
                'name.max' => 'لابد الايتجاوز عدد احرف الاسم 100 جرف ',
                'email.required' => '  البريد الالكتروني مطلوب',
                'email.email' => 'بريد الكتروني غير صحيح ',
                'email.unique' => ' البريد الالكتروني هذا مسجل من قبل لدينا  ',
                'mobile.required' => 'رقم الهاتف مطلوب ',
                'photo.mimes' => 'امتداد الصوره غير مسموح به',
                "password.required" => trans("admin/passwords.passwordRequired"),
                "password.confirmed" => trans("admin/passwords.confirmpassword"),
                "password.min" => trans("admin/passwords.confirmpassword")
            ];

            $rules = [
                'name' => 'required|max:100',
                'email' => 'required|email|unique:admins,email,' . $admin->id,
                'mobile' => 'required',
                'photo' => 'sometimes|nullable|mimes:jpg,jpeg,png',
            ];

            if ($request->filled('password')) {
                $rules['password'] = 'required|confirmed|min:6';
            }

            $validator = Validator::make($request->all(), $rules, $messages);

            if ($validator->fails()) {
                notify()->error('هناك خطا برجاء المحاوله مجددا ');
                return redirect()->back()->withErrors($validator)->withInput($request->all());
            }

            try {
                DB::beginTransaction();
                if (isset($request->photo) && !empty($request->photo)) {
                    $fileName = $this->uploadImage('admins', $request->photo);
                    $admin->update(['photo' => $fileName]);
                }
                $admin->update($request->except('photo', 'password'));

                if ($request->filled('password')) {
                    $admin->update([
                        "password" => $request->password ,
                    ]);
                }
                DB::commit();
                notify()->success('تم تحديث البيانات بنجاح ');
                return redirect()->route('admin.profile.edit')->with(['success' => 'تم تحديث البيانات بنجاح']);
            } catch (\Exception $ex) {
                DB::rollback();
                return abort('404');
            }
        } catch (\Exception $ex) {
            notify()->error('خطا في البيانات  برجاء المجاولة مجدا ');
            return abort('404');
        }
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
