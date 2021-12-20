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

    public function getService($data)
    {
        $query = $data['query']??null;
        $page = $data['page']??1;
        $size = $data['size']??10;
        $category = $data['catagory']??null;
        $sorting = $data['sorting']??null;

        $service = $this->serviceRepository->getBaseService($query,$page,$size,$category,$sorting);

        return [
            "page" => $page,
            "size" => $size,
            "total" => $service->count(),
            "data" => $service->get()
        ];
    }

    public function getCustomService($data)
    {
        $page = $data['page']??1;
        $size = $data['size']??10;
        
        $customService = $this->serviceRepository->getCustomService($page,$size);
        return [
            "page" => $page,
            "size" => $size,
            "total" => $customService->count(),
            "data" => $customService->get()
        ];

    }

}