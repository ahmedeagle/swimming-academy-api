<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Validator;
use Hash;

class LoginController extends Controller
{

    public function get_login()
    {
        return view("admin.auth.login");
    }

    public function store(Request $request)
    {
        Admin::create($request->all());
    }

    public function post_login(Request $request)
    {
        $messages = [
            'email.required' => 'البريد الإلكتروني مطلوب.',
            'email.email' => 'ادخل عنوان بريد إلكتروني صالح.',
            'password.required' => 'كلمة المرور مطلوبة.'
        ];

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ], $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $remember_me = $request->has('remember_me') ? true : false;

        if (auth()->guard('admin')->attempt(['email' => $request->input("email"), 'password' => $request->input("password")], $remember_me)) {
            notify()->success('تم الدخول بنجاح  ');
            return redirect() -> route('admin.dashboard');
        }
        notify()->error('خطا في البيانات  برجاء المجاولة مجدا ');
        return redirect()->back()->withErrors($validator)->withInput($request->all());
    }

    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
