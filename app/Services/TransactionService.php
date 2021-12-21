<?php

namespace App\Services;

use App\Repositories\PromoRepository;
use App\Repositories\ServiceRepository;

class TransactionService {

    protected $promoRepository;

    public function __construct(PromoRepository $promoRepository)
    {
        $this->promoRepository = $promoRepository;
    }

    public function getPromo($codePromo)
    {
        $promo = $this->promoRepository->getPromoByCodePromo($codePromo);

        return $promo;
    }

}
