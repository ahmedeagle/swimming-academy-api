<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscribtion extends Model
{
    protected $table = 'subscribtions';
    public $timestamps = true;

    protected $fillable = [
        'email', 'created_at', 'updated_at'
    ];

    protected $hidden = [
         'updated_at'
    ];


    public static function laratablesCustomAction($subscribe)
    {
        return view('subscribtions.actions', compact('subscribe'))->render();
    }

}
