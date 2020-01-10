<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCoach extends Model
{
    protected $table = 'users_coaches';
    public $timestamps = true;

    protected $fillable = ['user_id', 'coach_id'];

    protected $hidden = ['pivot'];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function coach()
    {
        return $this->belongsTo('App\Models\Coach', 'coach_id','id');
    }

}
