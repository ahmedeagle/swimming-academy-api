<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $table = 'mix';
    public $timestamps = true;

    protected $fillable = [
        'agreement_en',
        'agreement_ar',
        'reservation_rules_en',
        'reservation_rules_ar',
        'reservationNote_en',
        'reservationNote_ar',
        'provider_reg_rules_ar',
        'provider_reg_rules_en',
        'approve_message_ar',
        'approve_message_en',
        'title_ar',
        'title_en',
        'meta_keywords_ar',
        'meta_keywords_en',
        'meta_description_ar',
        'meta_description_en',
        'aboutApp_ar',
        'aboutApp_en',
        'app_text_ar',
        'app_text_en',
        'use1_ar',
        'use1_en',
        'use2_ar',
        'use2_en',
        'use3_ar',
        'use3_en',
        'email',
        'mobile',
        'address_ar',
        'address_en',
        'facebook',
        'twitter',
        'instg',
        'linkedIn',
        'whatsApp',
        'home_image1',
        'home_image2',
        'google_play',
        'app_store',
        'point_price',
        'dev_company_logo',
        'dev_company_ar',
        'dev_company_link',
        'dev_company_en'
    ];

    protected $forcedNullStrings = [
        'agreement_en',
        'agreement_ar',
        'reservation_rules_en',
        'reservation_rules_ar',
        'reservationNote_en',
        'reservationNote_ar',
        'provider_reg_rules_ar',
        'provider_reg_rules_en',
        'approve_message_ar',
        'approve_message_en',
        'title_ar',
        'title_en',
        'meta_keywords_ar',
        'meta_keywords_en',
        'meta_description_ar',
        'meta_description_en',
        'aboutApp_ar',
        'aboutApp_en',
        'app_text_ar',
        'app_text_en',
        'use1_ar',
        'use1_en',
        'use2_ar',
        'use2_en',
        'use3_ar',
        'use3_en',
        'email',
        'mobile',
        'address_ar',
        'address_en',
        'facebook',
        'twitter',
        'instg',
        'linkedIn',
        'whatsApp',
        'home_image1',
        'home_image2',
        'google_play',
        'app_store',
        'dev_company_link',
        'dev_company_logo',
        'dev_company_ar',
        'dev_company_en'
    ];


    public function setAttribute($key, $value)
    {
        if (in_array($key, $this->forcedNullStrings) && $value === null)
            $value = "";

        return parent::setAttribute($key, $value);
    }

    public function getHomeImage1Attribute($val)
    {
        return ($val != "" ? asset($val) : "");

    }

    public function getHomeImage2Attribute($val)
    {
        return ($val != "" ? asset($val) : "");

    }

    public function getDevCompanyLogoAttribute($val)
    {
        return ($val != "" ? asset($val) : "");

    }

}
