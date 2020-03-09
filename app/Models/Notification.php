<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = true;
    protected $fillable = ['title_ar', 'title_en', 'content_ar', 'content_en', 'notificationable_id', 'notificationable_type', 'created_at','updated_at','seen','seenByUser'];
    protected $forcedNullStrings = ['title_ar', 'title_en', 'content_ar', 'content_en', 'created_at'];
    protected $hidden = ['updated_at'];

    public function scopeCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('H:i Y-m-d');
    }

    public function notificationable()
    {
        return $this->morphTo();
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";

        return parent::setAttribute($key, $value);
    }

    public function scopeSelection($query){
        return $query -> select('title_'.app()->getLocale().' as title','title_'.app()->getLocale().' as content','created_at');
    }


      //new notifications to admin
    public function scopeNew($query){
        return $query -> where('notificationable_type','App\Models\Admin') -> where('seen','0');
    }
}


