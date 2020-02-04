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

    protected $forcedNullNumbers = ['attendances'];


    protected $fillable = [
        'user_id', 'start_date', 'end_date', 'team_id', 'price', 'status','attendances', 'created_at', 'updated_at'];

    protected $hidden = [
        'team_id'
    ];


    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullNumbers) && $value === null)
            $value = 0;
        return parent::setAttribute($key, $value);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function  team(){
        return $this->belongsTo('App\Models\Team', 'team_id', 'id');
    }


    public function getStartDateAttribute($value){
        return  date('Y-m-d',strtotime($value));
    }

    public function getEndDateAttribute($value){
        return  date('Y-m-d',strtotime($value));
    }

    public function getAttendancesAttribute($value){
       if($value === null){
           return 0;
       }
       return $value;
    }


    public function  scopeExpired($query){
        return $query ->where('status',0) -> where('end_date', '<', today()->format('Y-m-d'));
    }

    public function  scopeCurrent($query){
        return $query ->where('status',1);
    }
}
