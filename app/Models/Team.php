<?php

namespace App\Models;

 use App\Observers\TeamObserver;
 use App\Traits\GlobalTrait;
use Illuminate\Database\Eloquent\Model;
use DB;

class Team extends Model
{
    use  GlobalTrait;
    protected $table = 'teams';
    public $timestamps = true;
    protected $forcedNullStrings = ['photo', 'name_ar', 'name_en','level_ar','level_en'];
    protected $forcedNullNumbers = ['reservation_period'];
    protected $casts = [
        'status' => 'integer',
    ];

    protected $fillable = ['name_ar', 'name_en', 'photo', 'quotas', 'category_id','coach_id', 'status','level_ar','level_en'];

    protected $hidden = ['created_at', 'updated_at'];

    protected static function boot()
    {
        parent::boot();
        Team::observe(TeamObserver::class);
    }

    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }


    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id')->withDefault(["name" => ""]);
    }


    public function academy()
    {
        return $this->belongsTo('App\Models\Category', 'category_id')
            ->join('academies', 'academies.id', '=', 'categories.academy_id');
    }


    public function coach()
    {
        return $this->belongsTo('App\Models\Coach', 'coach_id', 'id');
    }

    public function heroes()
    {
        //return $this->hasManyThrough('App\Models\Hero', 'App\Models\User', 'team_id', 'user_id', 'id', 'id');
        return $this -> hasMany('App\Models\Hero','team_id','id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'team_id', 'id');
    }

    public function times()
    {
        return $this->hasOne('App\Models\TeamTime', 'team_id', 'id');
    }

    public  function  getLevelArAttribute($val){
         if ($val === null or  $val == ''){
             return "";
         }
         return $val;
    } public  function  getLevelEnAttribute($val){
         if ($val === null or  $val == ''){
             return "";
         }
         return $val;
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        else if (in_array($key, $this->forcedNullNumbers) && $value === null)
            $value = 0;
        return parent::setAttribute($key, $value);
    }

    public function getStatus()
    {
        return $this->status == 0 ? 'غير مفعل' : 'مفعل';
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'name_ar', 'name_en','level_ar','level_en', 'category_id','coach_id', 'photo', 'quotas', 'status');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function getTranslatedName()
    {
        return $this->{'name_' . app()->getLocale()};
    }

}

