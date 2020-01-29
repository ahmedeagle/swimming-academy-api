<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $timestamps = true;
    protected $fillable = ['imageable_id', 'imageable_type','photo','created_at'];
    protected $hidden = ['updated_at','created_at','imageable_id','imageable_type'];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");
    }

}


