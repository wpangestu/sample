<?php

namespace App\Repositories;

use App\Models\BaseService;
use App\Models\CategoryService;
use Illuminate\Database\Eloquent\Builder;

class ServiceRepository {

    protected $serviceCategory,$baseService;
    public function __construct(CategoryService $serviceCategory,BaseService $baseService)
    {
        $this->serviceCategory = $serviceCategory;
        $this->baseService = $baseService;
    }

    public function getServiceCategoryActive()
    {
        return $this->serviceCategory->where('status',1)->get();
    }

    public function getBaseService($query=null,$page = null,$limit = null)
    {
        $service = $this->baseService->whereHas('service_category', function (Builder $query) {
            $query->where('slug', '<>', 'custom');
        })->with('service_category')->latest();

        if(!is_null($query)){
            $service->where('name', 'like', '%' . $query . '%');
        }

        if($page != null && $limit != null){
            $service->limit($limit)->offset(($page - 1) * $limit);
        }

        return $service;
    }

}