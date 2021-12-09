<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Services\ServiceService;
use Illuminate\Http\Request;

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
}
