<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $timestamps = true;
    protected $fillable = ['title', 'ticketable_type', 'ticketable_id', 'created_at'];
    protected $hidden = ['updated_at'];

    public function imageable()
    {
        return $this->morphTo();
    }

}


