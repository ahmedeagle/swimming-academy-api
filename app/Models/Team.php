<?php

namespace App\Models;

use App\Traits\DoctorTrait;
use App\Traits\GlobalTrait;
use Illuminate\Database\Eloquent\Model;
use DB;

class Team extends Model
{
    use  GlobalTrait;
    protected $table = 'teams';
    public $timestamps = true;
    protected $forcedNullStrings = ['photo', 'name_ar', 'name_en'];
    protected $forcedNullNumbers = ['reservation_period'];
    protected $casts = [
        'status' => 'integer',
    ];

    protected $fillable = ['name_ar', 'name_en', 'photo', 'quotas', 'academy_id', 'status'];

    protected $hidden = [ 'created_at', 'updated_at'];

    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }

    public function academy()
    {
        return $this->belongsTo('App\Models\Academy', 'academy_id')->withDefault(["name" => ""]);
    }


    public function coaches()
    {
        return $this->belongsToMany('App\Models\Coach', 'teams_coaches', 'team_id', 'coach_id');
    }

    public function heroes()
    {

        return $this->hasMany('App\Models\Hero', 'team_id', 'id');
    }


    public function users()
    {
        return $this->hasMany('App\Models\User', 'team_id', 'id');
    }

    public function times()
    {
        return $this->hasOne('App\Models\TeamTime', 'team_id', 'id');
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
        return  $this -> status ==  0 ? 'غير مفعل' : 'مفعل';
    }

    public function scopeSelection($query)
    {
        return $query->select('id', 'name_ar', 'name_en', 'academy_id', 'photo', 'quotas', 'status');
    }

    public  function scopeActive($query)
    {
        return $query -> where('status',1);
    }

    public function getTranslatedName()
    {
        return $this->{'name_' . app()->getLocale()};
    }

}

