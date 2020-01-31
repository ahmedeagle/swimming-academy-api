<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use  DB;
use App\Observers\UserObserver;

class Subscription extends Model
{
    //
    protected $table = 'subscriptions';
    protected $casts = [
        'status' => 'integer',
    ];
    protected $appends = ['is_coach'];

    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'team_id', 'price', 'status', 'created_at', 'updated_at'];

    protected $hidden = [
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }
}
