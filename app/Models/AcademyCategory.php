<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcademyCategory extends Model
{
    protected $table = 'academies_categories';
    public $timestamps = true;

    protected $fillable = ['academy_id', 'category_id'];

    protected $hidden = ['pivot'];

    public function academy()
    {
        return $this->belongsTo('App\Models\Avademy', 'academy_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo('App\Models\Category', 'category_id');
    }
}
