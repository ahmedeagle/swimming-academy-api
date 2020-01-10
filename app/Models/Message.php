<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'messages';
    public $timestamps = true;
    protected $fillable = ['title', 'message', 'user_id', 'provider_id', 'manager_id', 'message_id', 'message_no', 'type', 'importance', 'order'];
    protected $hidden = ['user_id', 'updated_at', 'provider_id', 'manager_id', 'message_id'];

    public static function laratablesCustomUserAction($message)
    {
        return view('message.user.actions', compact('message'))->render();
    }

    public static function laratablesCustomProviderAction($message)
    {
        return view('message.provider.actions', compact('message'))->render();
    }

    public function laratablesCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('H:i Y-m-d');
    }

    public function laratablesType()
    {
        if($this->type == 1)
            return trans('messages.Inquiry');

        else if($this->type == 2)
            return trans('messages.Suggestion');

        else if($this->type == 3)
            return trans('messages.Complaint');

        else if($this->type == 4)
            return trans('messages.Others');

        return "";
    }

    public function laratablesImportance( )
    {
        if($this->importance == 1)
            return trans('messages.Quick');

        else if($this->importance == 2)
            return trans('messages.Normal');

        return "";
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id')->withDefault(["name" => ""]);
    }

    public function provider()
    {
        return $this->belongsTo('App\Models\Coach', 'provider_id')->withDefault(["name" => ""]);
    }

    public function manager()
    {
        return $this->belongsTo('App\Models\Manager', 'manager_id')->withDefault(["name" => ""]);
    }

    public function message()
    {
        return $this->belongsTo('App\Models\Message', 'message_id')->withDefault(["name" => ""]);
    }

    function messages()
    {
        return $this->hasMany('App\Models\Message', 'message_id', 'id');
    }
}
