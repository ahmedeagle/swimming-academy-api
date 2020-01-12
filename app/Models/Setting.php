<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'settings';
    public $timestamps = true;

    protected $fillable = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
     ];

    protected $forcedNullStrings = [
        'title_ar',
        'title_en',
        'content_ar',
        'content_en',
     ];


    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";

        return parent::setAttribute($key, $value);
    }


}
