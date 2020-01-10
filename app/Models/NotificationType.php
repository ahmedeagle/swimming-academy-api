<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    public $timestamps = true;
    protected $fillable = ['type', 'name_ar', 'name_en','created_at','updated_at'];
    protected $forcedNullStrings = ['name_ar', 'name_en', 'created_at'];
    protected $hidden = ['updated_at'];

    public function scopeCreatedAt()
    {
        return Carbon::parse($this->created_at)->format('H:i Y-m-d');
    }

    public function ticketable()
    {
        return $this->morphTo();
    }

    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }
}


