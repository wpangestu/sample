<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\BaseService;
use Illuminate\Http\Request;
use App\Services\ServiceService;
use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;

class ServiceController extends Controller
{
    //
    protected $serviceService;

    public function __construct(ServiceService $serviceService)
    {
        $this->serviceService = $serviceService;
    }

    public function serviceCategory()
    {
        try {
            $serviceCategory = $this->serviceService->getServiceCategory();

            $data = [];
            foreach ($serviceCategory as $val) {
                $data_temp = [
                    "id" => (int)$val->id,
                    "slug" => $val->slug,
                    "label" => $val->name,
                    "icon" => $val->icon ?? ""
                ];
                $data[] = $data_temp;
            }

            return response()->json($data);


        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function service_recommendation(Request $request)
    {
        try {
            $service = BaseService::whereHas('service_category', function (Builder $query) {
                $query->where('slug', '<>', 'custom');
            })->latest();

            if ($request->has('query')) {
                $query = $request->get('query');
                $service->where('name', 'like', '%' . $query . '%');
            }

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $service = $service->limit($limit)->offset(($page - 1) * $limit);
            $data = $service->get();
            $total = $service->count();

            $data_arr = [];
            foreach ($data as $key => $value) {

                $data_arr[] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "media" => $value->image,
                    "price" => (int)$value->price,
                    "category" => $value->service_category->name
                ];
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data_arr;

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function service(Request $request)
    {
        try {

            $q = $request->get('query');
            $category = $request->get('category');
            $sorting = $request->get('sorting');

            $service = BaseService::when($q, function ($query, $q) {
                return $query->where('name', 'like', '%' . $q . '%');
            })
                ->when($category, function ($query, $category) {
                    return $query->whereHas('service_category', function ($query) use ($category) {
                        $query->where('slug', $category);
                    });
                })
                ->when($sorting, function ($query, $sorting) {
                    if ($sorting == "price_asc") {
                        return $query->orderBy('price', 'asc');
                    } else {
                        return $query->orderBy('price', 'desc');
                    }
                });

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $service = $service->limit($limit)->offset(($page - 1) * $limit);
            $data = $service->get();
            $total = $service->count();

            $data_arr = [];
            foreach ($data as $key => $value) {
                # code...
                $data_arr[] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "media" => $value->image,
                    "price" => (int)$value->price
                ];
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data_arr;

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function service_detail($id)
    {
        try {
            //code...
            $service = BaseService::find($id);

            $data = [
                "id" => (int)$service->id,
                "name" => $service->name,
                "media" => $service->image,
                "price" => (int)$service->price,
                "guarantee" => (int)$service->long_guarantee ?? 0,
                "weight" => 0,
                "condition" => "new",
                "category" => [
                    "id" => (int)$service->service_category->id,
                    "slug" => $service->service_category->slug,
                    "label" => $service->service_category->name,
                    "icon" => $service->service_category->icon
                ],
                "description" => $service->description
            ];

            return response()->json($data);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json("Terjadi kesalahan " . $th->getMessage(), 422);
        }
    }
}
