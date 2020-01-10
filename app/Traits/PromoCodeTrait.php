<?php

namespace App\Traits;

use App\Models\Doctor;
use App\Models\Payment;
use App\Models\PromoCode;
use Carbon\Carbon;

trait PromoCodeTrait
{
    public function getPromoCodeByCode($promoCode){
        return PromoCode::where('code', $promoCode)->first();
    }

    public function getPromoCode($promoCode){
        return PromoCode::where('code', $promoCode)->whereDate('expired_at', '>=', Carbon::now()->format('Y-m-d'))->first();
    }

    public function checkPromoCode($promoCode, $doctorId, $providerId, $specifications = []){
        $promoCode = PromoCode::where('code', $promoCode)->whereDate('expired_at', '>=', Carbon::now()->format('Y-m-d'))
            ->where(function ($q) use ($doctorId, $providerId, $specifications){
                $q->where('doctor_id', $doctorId)->orWhere('provider_id', $providerId)->orWhereIn('specification_id', $specifications);
            })->first();
        if($promoCode != null)
            return true;

        return false;
    }

    public function getPromoByCode($promoCode, $doctorId , $provider = 0){
        return PromoCode::where([
                                    ['status',1],
                                    ['code', $promoCode]
                                ])
                         ->whereDate('expired_at', '>=', Carbon::now()->format('Y-m-d'))
                         ->whereHas('promocodedoctors', function($qq) use ($doctorId){
                                  $qq -> where('doctor_id',$doctorId);
                                  $qq -> where('status',1);
                             })
                       ->first();
    }


    public function getPaidPromoByCode($promoCode){
        return Payment::where('code', $promoCode)->first();
    }

    public function checkPaidPromoCodeConditions($offer_id,$doctorId){

        return PromoCode::where([
            ['status',1],
            ['id', $offer_id]
        ])
            ->whereDate('expired_at', '>=', Carbon::now()->format('Y-m-d'))
            ->whereHas('promocodedoctors', function($qq) use ($doctorId){
                $qq -> where('doctor_id',$doctorId);
                $qq -> where('status',1);
            })
            ->where('available_count' ,'>', 0)
            ->first();

    }




}
