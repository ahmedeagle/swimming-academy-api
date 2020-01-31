<?php

namespace App\Traits;

use App\Models\Academy;
use App\Models\Category;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

trait AcademyTrait
{


    public function getAllAcademies()
    {
        return Academy::active()->select('id', DB::raw('name_' . $this->getCurrentLang() . ' as name'), 'code')->get();
    }

    public function getAcademyCategoriesByCode($academyCode)
    {
        $academy = Academy::where('code', $academyCode)->first();
        return $categories = $academy->categories()->select('id', DB::raw('name_' . app()->getLocale()) . ' as name')->get();
    }

    public function getCategoryTeamsById($categoryId)
    {
        $category = Category::find($categoryId);
        return $teams = $category->teams()->select('id','photo',DB::raw('name_' . app()->getLocale() . ' as name'),DB::raw('level_' . app()->getLocale() . ' as level') )->get();
    }


}
