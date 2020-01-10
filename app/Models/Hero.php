<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'heroes';
    public $timestamps = true;
    protected $forcedNullStrings = ['date'];

    protected $fillable = ['user_id', 'team_id', 'date'];
    protected $hidden = ['created_at', 'updated_at'];

    public function team(){
        return $this -> belongsTo('App\Models\Team','tema_id','id');
    }
    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        else if (in_array($key, $this->forcedNullNumbers) && $value === null)
            $value = 0;

        return parent::setAttribute($key, $value);
    }
}
