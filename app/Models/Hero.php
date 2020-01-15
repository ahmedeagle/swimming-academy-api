<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'heroes';
    public $timestamps = true;
    protected $forcedNullStrings = ['date'];

    protected $fillable = ['user_id', 'team_id', 'date','created_at'];
    protected $hidden = [ 'updated_at'];


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
}
