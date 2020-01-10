<?php

namespace App\Traits;

use App\Models\CustomPage;
use DB;

trait CustomPagesTrait
{
    public function getAllCustomPages(){
        return CustomPage::all();
    }

    public function getProviderCustomPages(){
        return CustomPage::where('status', true)->where('provider', true)
            ->select('id', DB::raw('title_' . $this->getCurrentLang() . ' as title'))->get();
    }

    public function getUserCustomPages(){
        return CustomPage::where('status', true)->where('user', true)
            ->select('id', DB::raw('title_' . $this->getCurrentLang() . ' as title'))->get();
    }

    public function getProviderPageById($id){
        return CustomPage::where('status', true)->where('provider', true)->where('id', $id)
            ->select('id', DB::raw('title_' . $this->getCurrentLang() . ' as title'), DB::raw('content_' . $this->getCurrentLang() . ' as content'))->first();
    }

    public function getUserPageById($id){
        return CustomPage::where('status', true)->where('user', true)->where('id', $id)
            ->select('id', DB::raw('title_' . $this->getCurrentLang() . ' as title'), DB::raw('content_' . $this->getCurrentLang() . ' as content'))->first();
    }

}