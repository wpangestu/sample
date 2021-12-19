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

    public function getServiceRecommendation($data)
    {
        $query = $data['query']??null;
        $page = $data['page']??1;
        $size = $data['size']??10;

        $service = $this->serviceRepository->getBaseService($query,$page,$size);

        return [
            "page" => $page,
            "size" => $size,
            "total" => $service->count(),
            "data" => $service->get()
        ];

    }

}