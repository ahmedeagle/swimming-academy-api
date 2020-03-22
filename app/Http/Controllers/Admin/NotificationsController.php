<?php

namespace App\Http\Controllers\Admin;

use App\Models\Academy;
use App\Models\Category;
use App\Models\Coach;
use App\Models\Notification;
use App\Models\Team;
use App\Models\TeamTime;
use App\Models\Time;
use App\Traits\Dashboard\PublicTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Admin;
use DB;
use Auth;
use Carbon\Carbon;
use Namshi\JOSE\Signer\SecLib\RS384;
use Validator;
use Hash;

class NotificationsController extends Controller
{

    use PublicTrait;

    public function index()
    {
        $notifications = Notification::admin()->latest() -> paginate(25);
        Notification::where('seen','0')->update(['seen'=> '1']);
        return view('admin.notifications.index',compact('notifications'));

    }
}
