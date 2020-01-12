<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Academy extends Model
{
    protected $table = 'academies';
    public $timestamps = true;

    protected $casts = [
        'status' => 'integer',
    ];

    protected $fillable = ['name_ar','name_en','address','status','address_ar','address_en'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $forcedNullStrings = ['name','address_ar','address_en'];
     public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }

    public function getStatus()
    {
       return  $this -> status ==  0 ? 'غير مفعل' : 'مفعل';
    }

    public  function scopeActive($query)
    {
        return $query -> where('status',1);
    }

}
