<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TeamTime extends Model
{
    protected $table = 'teams_days';
    public $timestamps = true;


    protected $fillable = ['saturday_start_work', 'saturday_end_work', 'sunday_start_work', 'sunday_end_work', 'monday_start_work', 'monday_end_work', 'tuesday_start_work', 'tuesday_end_work', 'wednesday_start_work', 'wednesday_end_work', 'thursday_start_work', 'thursday_end_work', 'friday_start_work', 'friday_end_work', 'created_at', 'updated_at', 'team_id', 'saturday_status', 'sunday_status', 'monday_status', 'tuesday_status', 'wednesday_status', 'thursday_status', 'friday_status'];
    protected $forced24DateFormate = ['saturday_start_work', 'saturday_end_work', 'sunday_start_work', 'sunday_end_work', 'monday_start_work', 'monday_end_work', 'tuesday_start_work', 'tuesday_end_work', 'wednesday_start_work', 'wednesday_end_work', 'thursday_start_work', 'thursday_end_work', 'friday_start_work', 'friday_end_work'];


    protected $hidden = ['team_id', 'updated_at', 'created_at'];

    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id');
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forced24DateFormate))
            $value = date("H:i", strtotime($value));
        return parent::setAttribute($key, $value);
    }

}
