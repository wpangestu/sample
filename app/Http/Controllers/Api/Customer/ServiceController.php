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

    public function service_category()
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

            $requestServiceRecommendation = $request->only('query','page','size');
            $serviceRecommendation = $this->serviceService->getServiceRecommendation($requestServiceRecommendation);
            $data = $serviceRecommendation['data']->map(function ($value) {
                return [
                        "id" => $value->id,
                        "name" => $value->name,
                        "media" => $value->image,
                        "price" => (int)$value->price,
                        "category" => $value->service_category->name
                ];
            });
            
            $response['page'] = (int)$serviceRecommendation['page'];
            $response['size'] = (int)$serviceRecommendation['size'];
            $response['total'] = (int)$serviceRecommendation['total'];
            $response['data'] = $data;
    
            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function service(Request $request)
    {
        try {

            $requestService = $request->only('query','page','size','category','sorting');
            $service = $this->serviceService->getService($requestService);
            $data = $service['data']->map(function($value){
                return [
                    "id" => (int)$value->id,
                    "name" => $value->name,
                    "media" => $value->image,
                    "price" => (int)$value->price
                ];
            });

            $response['page'] = (int)$service['page'];
            $response['size'] = (int)$service['size'];
            $response['total'] = (int)$service['total'];
            $response['data'] = $data;

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

    public function get_custom_category(Request $request)
    {
        try {

            $requestCustomService = $request->only('page','size');
            $customService = $this->serviceService->getCustomService($requestCustomService);
            $data = $customService['data']->map(function($value){
                return [
                    "id" => (int)$value->id,
                    "name" => $value->name,
                    "item_name" => "",
                    "media" => $value->image
                ];
            });

            $response['page'] = (int)$customService['page'];
            $response['size'] = (int)$customService['size'];
            $response['total'] = (int)$customService['total'];
            $response['data'] = $data;            

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }
}
