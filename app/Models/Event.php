<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    public $timestamps = true;
    protected $forcedNullStrings = ['title_ar', 'title_en', 'photo', 'description_ar', 'description_en', 'status'];
    protected $fillable = ['title_ar', 'title_en', 'photo', 'description_ar', 'description_en', 'status','academy_id','category_id'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'status' => 'integer',
    ];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }

    public function getStatus()
    {
        return $this->status == 0 ? 'غير مفعل' : 'مفعل';
    }

    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    public function academy()
    {
        return $this->belongsTo('App\Models\Academy', 'academy_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

}
