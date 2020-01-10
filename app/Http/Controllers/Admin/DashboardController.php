<?php

namespace App\Http\Controllers\Admin;

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
    public function dashboard(){
        return view('admin.dashboard.index');
    }
}
