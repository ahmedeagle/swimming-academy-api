<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    public $timestamps = true;
    protected $fillable = ['title_ar', 'title_en', 'content_ar', 'content_en', 'notificationable_id', 'notificationable_type', 'created_at'];
    protected $hidden = ['updated_at'];

    public function scopeCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('H:i Y-m-d');
    }

    public function ticketable()
    {
        return $this->morphTo();
    }


}


