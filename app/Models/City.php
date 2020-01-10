<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    public $timestamps = true;

    protected $fillable = ['name'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $forcedNullStrings = ['name'];


    public function users()
    {
        return $this->hasMany('App\Models\User', 'city_id');
    }
}
