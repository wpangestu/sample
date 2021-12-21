<?php

namespace App\Http\Controllers\Api\Customer;

use Illuminate\Http\Request;
use App\Services\ServiceService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Services\TransactionService;

class TransactionController extends Controller
{
    //
    protected $serviceService,$transactionService;

    public function __constructor(ServiceService $serviceService,TransactionService $transactionService){
        $this->serviceService = $serviceService;
        $this->transactionService = $transactionService;
    }

    public function order_generate_payment(Request $request)
    {
        try {
            //code...
            DB::beginTransaction();

            $services = $request->get('services');
            $custom_category = $request->get('custom_category');

            $totalServicePrice = 0;

            if (isset($custom_category)) {
                $dataCustomService = [
                    "service_id" => $custom_category,
                    "quantity" => 1
                ];
                $totalServicePrice += $this->serviceService->getTotalPriceService($dataCustomService);
            } elseif (isset($services)) {
                $totalServicePrice += $this->serviceService->getTotalPriceService($services);
            }

            $shipping = 0;
            $unique_code = mt_rand(100, 999);

            $total_price = $totalServicePrice + $shipping + $unique_code;

            if ($request->has('promo_code')) {
                $promo_code = $request->get('promo_code');
                $promo = $this->transactionService->getPromo($promo_code);

                if (!is_null($promo)) {
                    $promo_res = [
                        "promo" => (int)$promo->value,
                        "message" => "Kodo promo aktif",
                        "positive" => true
                    ];
                } else {
                    $promo_res = [
                        "promo" => 0,
                        "message" => "Promo Tidak ditemukan",
                        "positive" => false
                    ];
                }
            } else {
                $promo_res = null;
            }

            $response = [
                "total_service_price" => (int)$totalServicePrice,
                "price_distance" => (int)$shipping,
                "unique_code" => (int)$unique_code,
                "total_price" => (int)$total_price - ($promo_res["promo"] ?? 0),
                "promo_info" => $promo_res
            ];

            DB::commit();

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }
}
