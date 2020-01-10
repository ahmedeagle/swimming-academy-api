<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Coach extends Authenticatable implements JWTSubject
{
    use Notifiable;
    protected $table = 'coahes';
    public $timestamps = true;
    //because value may send in 1 or '1'  i ensure it is integr only accept 1 not '1'
    protected $casts = [
        'status' => 'integer',
    ];

    protected $forcedNullStrings = ['name_ar', 'name_en', 'photo', 'mobile', 'device_token', 'api_token'];
    protected $forcedNullNumbers = ['rate', 'gender', 'status'];

    protected $fillable = ['name_ar', 'name_en', 'photo', 'mobile', 'academy_id', 'gender', 'password', 'device_token', 'status', 'created_at', 'api_token', 'created_at', 'rate'];

    protected $hidden = [
        'created_at', 'password', 'updated_at', 'device_token'
    ];


    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }

    public function academy()
    {
        return $this->belongsTo('App\Models\Academy', 'academy_id', 'id');
    }



    public function teams()
    {
        return $this->belongsToMany('App\Models\Team', 'teams_coaches', 'coach_id', 'team_id');
    }


    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'users_coaches', 'coach_id', 'user_id');
    }


    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        else if (in_array($key, $this->forcedNullNumbers) && $value === null)
            $value = 0;

        return parent::setAttribute($key, $value);
    }


    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

    public function tokens()
    {
        return $this->hasMany('App\Models\Token');
    }

    public function getStatusAttribute($status)
    {
        return $status == 0 ? 'غير مفعل' : 'مفعل';
    }


    public function getGenderAttribute($status)
    {
        return $status == 1 ? 'ذكر' : 'أنثي';
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'name_ar', 'name_en', 'academy_id', 'photo', 'mobile','gender','device_token','rate', 'status');
    }

    public  function scopeActive($query)
    {
        return $query -> where('status',1);
    }

}
