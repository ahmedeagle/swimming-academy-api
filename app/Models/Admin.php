<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable implements JWTSubject
{
    use Notifiable, HasRoles;
    protected $table = 'admins';
    public $timestamps = true;
    protected $forcedNullStrings = ['photo', "name", 'email', 'mobile'];

    protected $fillable = ['name', 'photo', 'mobile', 'email', 'password', 'api_token', 'activation_code'
    ];

    protected $hidden = [
        'password', 'remember_token', 'api_token', 'id', 'activation_code'
    ];


    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";
        return parent::setAttribute($key, $value);
    }


    public function getPhotoAttribute($val)
    {
        return ($val != "" ? asset($val) : asset('assets/admin/images/portrait/small/avatar-s-19.png'));
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function setPasswordAttribute($password)
    {
        if (!empty($password)) {
            $this->attributes['password'] = bcrypt($password);
        }
    }

}
