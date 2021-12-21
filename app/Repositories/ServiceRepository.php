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

    public function getBaseService($query=null, $page = null, $limit = null,$category=null,$sorting=null)
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

        $service->when($category, function ($query, $category) {
            return $query->whereHas('service_category', function ($query) use ($category) {
                $query->where('slug', $category);
            });
        });
        $service->when($sorting, function ($query, $sorting) {
            if ($sorting == "price_asc") {
                return $query->orderBy('price', 'asc');
            } else {
                return $query->orderBy('price', 'desc');
            }
        });
        return $service;
    }

    public function getCustomService($page=null,$limit=null)
    {
        $service = BaseService::whereHas('service_category', function ($query) {
            $query->where('name', 'like', '%custom%');
        });

        if($page != null && $limit != null){
            $service->limit($limit)->offset(($page - 1) * $limit);
        }

        return $service;
    }

    public function getPriceByServiceId($id):int
    {
        $service = BaseService::find($id);
        return $service->price;
    }

}