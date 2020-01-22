<?php

namespace App\Models;

use App\Observers\CategoryObserver;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use  DB;
use App\Observers\UserObserver;
class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    //
    protected $table = 'users';
    protected $forcedNullStrings = ['name_ar', 'name_en', 'address_ar', 'address_en', 'mobile', 'email', 'tall', 'weight', 'birth_date', 'device_token', 'activation_code', 'photo', 'api_token', 'subscribed'];
    protected $casts = [
        'status' => 'integer',
        'team_id' => 'integer',
        'academy_id' => 'integer',
        'subscribed' => 'integer'
    ];
    protected $appends = ['is_coach'];

    protected $fillable = [
        'name_ar', 'name_en', 'address_ar', 'address_en', 'mobile', 'team_id','category_id','academy_id','email', 'tall', 'weight', 'birth_date', 'status', 'device_token',
        'activation_code', 'photo', 'api_token', 'password', 'created_at', 'updated_at'];

    protected $hidden = [
        'updated_at', 'password', 'device_token', 'created_at'
    ];


    protected static function boot()
    {
        parent::boot();
        User::observe(UserObserver::class);
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Team', 'team_id')
            ->join('categories', 'categories.id', '=', 'teams.category_id');
    }

    public function  academy(){
        return $this -> belongsTo('App\Models\Academy','academy_id','id');
    }

    public function Coaches()
    {
        return $this->belongsToMany('App\Models\Coach', 'users_coaches', 'user_id', 'coach_id');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id', 'id');
    }


    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
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
        return $this->hasMany('App\Models\UserToken');
    }


    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function scopeSubScribed($query)
    {
        return $query->where('subscribed', 1);
    }

    public function getStatus()
    {
        return $this->status == 0 ? 'غير مفعل' : 'مفعل';
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'name_ar', 'name_en', 'address_ar', 'address_en', 'mobile', 'email', 'tall', 'weight', 'birth_date', 'status', 'academy_id', 'team_id', 'device_token', 'photo');
    }

    public function scopeSelectionByLang($query)
    {
        return $query->select('id', 'team_id', DB::raw('name_' . app()->getLocale() . ' as name'), DB::raw('address_' . app()->getLocale() . ' as address'), 'mobile', 'email', 'tall', 'weight', 'birth_date', 'photo');
    }

    public function tickets()
    {
        return $this->morphMany('\App\Models\Ticket', 'ticketable');
    }

    public function notifications()
    {
        return $this->morphMany('\App\Models\Notification', 'notificationable');
    }

    public function getIsCoachAttribute()
    {
        return 0;
    }

    public function getTranslatedName()
    {
        return $this->{'name_' . app()->getLocale()};
    }

    public function heroes()
    {
        $weekStartEnd = currentWeekStartEndDate();
        $startWeek = date('Y-m-d', strtotime($weekStartEnd['startWeek']));
        $endWeek = date('Y-m-d', strtotime($weekStartEnd['endWeek']));
        return $this->hasMany('App\Models\Hero', 'user_id', 'id')->whereBetween('created_at', [$startWeek, $endWeek]);
    }
}
