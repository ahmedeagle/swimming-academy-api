<?php

namespace App\Http\Controllers\Admin;

use App\Models\Admin;
use App\Notifications\AdminPasswordReset;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Validator;
use DB;
use Carbon\Carbon;

class ForgetPasswordController extends Controller
{
    use PublicTrait;

    public function get_forget_password()
    {
        return view("admin.auth.forget-password");
    }

    public function post_forget_password(Request $request)
    {
        $rules = [
            "email" => "required|email|exists:admins,email"
        ];
        $messages = [
            "required" => trans("admin/passwords.emailRequired"),
            "email" => trans("admin/passwords.email"),
            "exists" => trans("admin/passwords.emailExists"),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $email = $request->input('email');
        $admin = Admin::where("email", $email)->first();
        $code = $this->getRandomString(6);
        $message = $code . "رقم الدخول الخاص بك هو :- ";
        $admin->update(['activation_code' => $code]);
        try {
            $admin->notify(new AdminPasswordReset($code));
           // Mail::to($reservation->user->email)->send(new AcceptReservationMail($reservation->reservation_no));
        } catch (\Exception $ex) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        notify()->success('تم أرسال كود التفعيل الي بريدك الالكتروني ');

        return redirect()->route('admin.get.codeconfirmation')->with(['success' => 'تم أرسال كود التفعيل الي بريدك الالكتروني ']);
    }

    public function get_code_confirmation($code = null)
    {
        return view("admin.auth.code-confirmation") -> with('code',$code);
    }


    public function confirmCode(Request $request)
    {

        $rules = [
            "activation_code" => "required"
        ];
        $messages = [
            "required" => trans("admin/passwords.activationcoderequired"),
        ];

        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }
        $admin = Admin::where('activation_code', $request->activation_code)->first();
        if (!$admin) {
            notify()->error(' الكود المدخل غير صحيح ');
            return redirect()->back()->with(['error' => 'الكود المدخل غير صحيح']);
        }
        notify()->success(' تم تاكيد كود التفعيل الخاص بكم ');
        return redirect()->route('admin.get.passwordreset', $request->activation_code)->with(['success' => 'تم تاكيد كود التفعيل الخاص بكم']);
    }

    public function get_password_reset($activation_code)
    {
        return view("admin.auth.reset-password")->with('activation_code', $activation_code);
    }

    public function password_reset(Request $request)
    {
        $rules = [
            "password" => "required||min:6|confirmed"
        ];
        $messages = [
            "required" => trans("admin/passwords.passwordRequired"),
            "confirmed" => trans("admin/passwords.confirmpassword"),
            "min" => trans("admin/passwords.confirmpassword"),

        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            notify()->error('هناك خطا برجاء المحاوله مجددا ');
            return redirect()->back()->withErrors($validator)->withInput($request->all());
        }

        $password = $request->input('password');
        $activation_code = $request->input('activation_code');

        Admin::where("activation_code", $activation_code)
            ->update([
                "password" => bcrypt($password),
                "activation_code" => null
            ]);
        notify()->success(trans("admin/passwords.passwordChanged"));
        return redirect()->route('admin.login')->with("success", trans("admin/passwords.passwordChanged"));
    }
}
