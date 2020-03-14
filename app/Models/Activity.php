<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    public $timestamps = true;
    protected $forcedNullStrings = ['title_ar', 'title_en', 'videoLink'];
    protected $casts = [
        'status' => 'integer',
    ];
    protected $fillable = ['title_ar', 'title_en', 'videoLink', 'created_at', 'status', 'academy_id', 'category_id', 'team_id'];
    protected $hidden = ['created_at', 'updated_at'];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }

    public function academy()
    {
        return $this->belongsTo('App\Models\Academy', 'academy_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');

    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id', 'id');
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
