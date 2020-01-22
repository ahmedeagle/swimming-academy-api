<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'heroes';
    public $timestamps = true;
    protected $forcedNullStrings = ['date','note_ar','note_en'];

    protected $fillable = ['user_id', 'team_id', 'category_id', 'date', 'created_at','note_ar','note_en'];
    protected $hidden = ['updated_at','note_ar','note_en'];

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

    public function academy()
    {
        return $this->belongsTo('App\Models\User', 'user_id')
            ->join('academies', 'academies.id', '=', 'users.academy_id');
    }


    public function team()
    {
        return $this->belongsTo('App\Models\Team', 'team_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id', 'id');
    }

}
