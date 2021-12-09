<?php

namespace App\Repositories;

use App\Models\CategoryService;

class ServiceRepository {

    protected $serviceCategory;
    public function __construct(CategoryService $serviceCategory)
    {
        $this->serviceCategory = $serviceCategory;
    }

    public function getServiceCategoryActive()
    {
        return $this->serviceCategory->where('status',1)->get();
    }

}