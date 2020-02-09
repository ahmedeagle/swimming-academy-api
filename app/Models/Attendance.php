<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Observers\AcademyObserver;
use App\Models\Subscription;

class Attendance extends Model
{
    protected $table = 'attendance';
    public $timestamps = true;

    protected $fillable = ['user_id', 'team_id', 'subscription_id', 'date', 'attend', 'created_at', 'updated_at'];
    protected $hidden = ['created_at', 'updated_at'];

    public function teams()
    {
        return $this->belongsTo(
            'App\Models\Team','team_id','id'
        );
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id', 'id');
    }


    public function subscription()
    {
        return $this->belongsTo(Subscription::class, 'user_id', 'id');
    }
}
