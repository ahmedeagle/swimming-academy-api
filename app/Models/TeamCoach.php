<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeamCoach extends Model
{
    protected $table = 'teams_coaches';
    public $timestamps = true;

    protected $fillable = ['coach_id', 'team_id'];

    protected $hidden = ['pivot'];

    public function coach()
    {
        return $this->belongsTo('App\Models\Coach', 'coach_id', 'id');
    }

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }

}
