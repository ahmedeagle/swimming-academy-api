<?php

namespace App\Traits;

 use App\Models\Academy;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait AcademyTrait
{


    public  function getAllAcademies(){
        return Academy::active() -> select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'))->get();
    }

}
