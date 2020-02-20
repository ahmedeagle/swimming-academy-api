<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Time extends Model
{
    protected $table = 'times';
    public $timestamps = true;

    protected $fillable = ['day_name', 'day_code', 'from_time', 'to_time', 'team_id','status'];
    protected $hidden = ['team_id', 'updated_at', 'created_at','status'];

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }

    public function times()
    {
        return $this->hasMany('App\Models\Time', 'team_id', 'id');
    }

    public function TimesCode()
    {
        return $this->hasMany('App\Models\Time', 'team_id', 'id');
    }
}
