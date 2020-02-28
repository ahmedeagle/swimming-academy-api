<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AcademyObserver;

class Rate extends Model
{
    protected $table = 'rates';
    public $timestamps = true;

    protected $fillable = ['rate', 'comment', 'user_id', 'team_id', 'coach_id', 'rateable', 'day_name', 'date','subscription_id'];
    protected $forcedNullStrings = ['comment'];

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }

    public function coach()
    {
        return $this->belongsTo('App\Models\Coach', 'coach_id', 'id');
    }


    public  function scopeCoaches($query){
        return $this -> where('rateable',0);
    }

    public  function scopeUsers($query){
        return $this -> where('rateable',1);
    }

    public function team(){
        return $this -> belongsTo('App\Models\Team','team_id','id');
    }
}
