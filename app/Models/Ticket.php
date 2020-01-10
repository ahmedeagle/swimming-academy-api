<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    public $timestamps = true;
    protected $fillable = ['title', 'ticketable_type', 'ticketable_id', 'message_no', 'type', 'importance', 'solved', 'created_at'];
    protected $hidden = ['updated_at'];

    public function scopeCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('H:i Y-m-d');
    }



    public function ticketable()
    {
        return $this->morphTo();
    }

    public function getTypeAttribute($type)
    {
        if ($type == 1)
            return trans('messages.Inquiry');

        else if ($type == 2)
            return trans('messages.Suggestion');

        else if ($type == 3)
            return trans('messages.Complaint');

        else if ($type == 4)
            return trans('messages.Others');

        return "";
    }

    public function getImportanceAttribute($importance)
    {
        if ($importance == 1)
            return trans('messages.Quick');

        else if ($importance == 2)
            return trans('messages.Normal');
        return "";
    }


    public function replies()
    {
        return $this->hasMany('App\Models\Replay', 'ticket_id', 'id');
    }


    function messages()
    {
        return $this->hasMany('App\Models\Replay', 'ticket_id', 'id');
    }



}


