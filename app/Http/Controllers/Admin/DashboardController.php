<?php

namespace App\Http\Controllers\Admin;

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
        return view('admin.aboutus.edit', compact('settings'));
    }

    public function saveAboutUs(Request $request)
    {
        $settings = Setting::first();
        if ($settings) {
            $settings->update($request->all());
        } else {
            Setting::create($request->all());
        }
        notify()->success('تمت التعديل  بنجاح ');
        return redirect()->route('admin.aboutus');
    }

}
