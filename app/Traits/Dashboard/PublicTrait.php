<?php

namespace App\Traits\Dashboard;

use App\Models\Admin;
use App\Models\City;
use App\Models\District;
use App\Models\Doctor;
use App\Models\InsuranceCompany;
use App\Models\Manager;
use App\Models\Nationality;
use App\Models\Nickname;
use App\Models\PromoCodeCategory;
use App\Models\Provider;
use App\Models\ProviderType;
use App\Models\Reservation;
use App\Models\Specification;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use DB;
use PhpParser\Comment\Doc;

trait PublicTrait
 {
    function getRandomString($length)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $string = '';
        for ($i = 0; $i < $length; $i++) {
            $string .= $characters[mt_rand(0, strlen($characters) - 1)];
        }
        $chkCode = Admin::where('activation_code', $string)->first();
        if ($chkCode) {
            $this->getRandomString(6);
        }
        return $string;
    }

    public function uploadImage($folder, $image)
    {
        $image->store('/', $folder);
        $filename = $image->hashName();
        $path = 'images/' . $folder . '/' . $filename;
        return $path;
    }


}
