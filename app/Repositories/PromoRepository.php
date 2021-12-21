<?php

namespace App\Repositories;

use App\Models\Promo;

class PromoRepository {

    protected $promo;
    public function __construct(Promo $promo)
    {
        $this->promo = $promo;
    }

    public function getPromoByCodePromo($codePromo)
    {
        return $this->promo->where('code',$codePromo)
                    ->where('is_active',true)
                    ->first();
    }
}