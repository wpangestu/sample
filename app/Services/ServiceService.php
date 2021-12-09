<?php

namespace App\Services;

use App\Repositories\ServiceRepository;

class ServiceService {

    protected $serviceRepository;

    public function __construct(ServiceRepository $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    public function getServiceCategory()
    {
        return $this->serviceRepository->getServiceCategoryActive();
    }

}