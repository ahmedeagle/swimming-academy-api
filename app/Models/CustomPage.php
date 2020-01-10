<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPage extends Model
{
    protected $table = 'custom_pages';
    public $timestamps = true;

    protected $fillable = ['title_en', 'title_ar', 'content_en', 'content_ar', 'status', 'provider', 'user'];

    protected $hidden = ['created_at', 'updated_at'];

    public static function laratablesCustomAction($customPage)
    {
        return view('customPage.actions', compact('customPage'))->render();
    }

    public function laratablesStatus( )
    {
        return ($this->status ? 'منشورة' : 'غير منشورة');
    }

    public function laratablesUser( )
    {
        return ($this->user ? 'نعم' : '');
    }

    public function laratablesProvider( )
    {
        return ($this->provider ? 'نعم' : '');
    }
}
