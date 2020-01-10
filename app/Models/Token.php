<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public $timestamps = true;

    protected $fillable = ['coach_id', 'api_token'];

    protected $hidden = ['created_at', 'updated_at'];

    public function coach()
    {
        return $this->belongsTo('App\Models\Coach');
    }
}
