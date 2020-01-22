<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AcademyObserver;

class Academy extends Model
{
    protected $table = 'academies';
    public $timestamps = true;

    protected $casts = [
        'status' => 'integer',
    ];

    protected $fillable = ['name_ar', 'name_en', 'address', 'status', 'address_ar', 'address_en', 'code', 'logo'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $forcedNullStrings = ['name', 'address_ar', 'address_en', 'code', 'logo'];


    protected static function boot()
    {
        parent::boot();
        Academy::observe(AcademyObserver::class);
    }


    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }

    public function categories()
    {
        // return $this->belongsToMany('App\Models\Category', 'academies_categories', 'academy_id', 'category_id');
        return $this->hasMany('App\Models\Category', 'academy_id', 'id');
    }


    public function teams()
    {
        return $this->hasManyThrough(
            'App\Models\Team',
            'App\Models\Category',
            'academy_id',
            'category_id',
            'id',
            'id'
        );
    }


    public function getLogoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }

    public function heroes()
    {
        return $this->hasManyThrough('App\Models\Hero', 'App\Models\User', 'academy_id', 'user_id', 'id', 'id');
    }


    public function activities()
    {
        return $this->hasMany('App\Models\Activity', 'academy_id', 'id');
    }

    public function users()
    {
        return $this->hasMany('App\Models\User', 'academy_id', 'id');
    }

    public function coaches()
    {
        return $this->hasMany('App\Models\Coach', 'academy_id', 'id');
    }

    public function events()
    {
        return $this->hasMany('App\Models\Event', 'academy_id', 'id');
    }


    public function setting()
    {
        return $this->hasOne('App\Models\Setting', 'academy_id', 'id');
    }

    public function getStatus()
    {
        return $this->status == 0 ? 'غير مفعل' : 'مفعل';
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

}
