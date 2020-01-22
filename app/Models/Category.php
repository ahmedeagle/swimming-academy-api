<?php

namespace App\Models;

use App\Observers\AcademyObserver;
use Illuminate\Database\Eloquent\Model;
use App\Observers\CategoryObserver;

class Category extends Model
{
    protected $table = 'categories';
    public $timestamps = true;

    protected $casts = [
        'status' => 'integer',
    ];

    protected $fillable = ['name_ar', 'name_en', 'status', 'academy_id'];
    protected $hidden = ['created_at', 'updated_at'];


    protected static function boot()
    {
        parent::boot();
        Category::observe(CategoryObserver::class);
    }

    public function academy()
    {
        //return $this->belongsToMany('App\Models\Academy', 'academies_categories', 'category_id', 'academy_id');
        return $this->belongsTo('App\Models\Academy', 'academy_id', 'id');
    }


    public function teams()
    {
        return $this->hasMany('App\Models\Team', 'category_id', 'id');
    }


    public function events()
    {
        return $this->hasMany('App\Models\Event', 'category_id', 'id');
    }


    public function activities()
    {
        return $this->hasMany('App\Models\Activity', 'category_id', 'id');
    }

    public function users()
    {
        return $this->hasManyThrough(
            'App\Models\user',
            'App\Models\Team',
            'category_id',
            'team_id',
            'id',
            'id'
        );
    }


    public function allUsers()
    {
        return $this->hasMany('App\Models\User', 'category_id', 'id');

    }

    public function coaches()
    {
        return $this->hasMany('App\Models\Coach', 'category_id', 'id');

    }

    public function heroes()
    {
        //return $this->hasManyThrough('App\Models\Hero', 'App\Models\User', 'team_id', 'user_id', 'id', 'id');
        return $this->hasMany('App\Models\Hero', 'category_id', 'id');
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
