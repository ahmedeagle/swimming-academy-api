<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reciever extends Model
{
    protected $table = 'admin_notifications_receivers';
    public $timestamps = true;

    protected $fillable = ['notification_id', 'actor_id','seen','created_at'];
    protected  $hidden =['updated_at'];

    public function user()
    {
        return $this->belongsTo('App\Models\User','actor_id');
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Provider','actor_id');
    }

}
