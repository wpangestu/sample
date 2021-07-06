<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Withdraw;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReviewService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    //
    public function index(Request $request)
    {

        try {

            $user_id = auth()->user()->id;
            $data = Notification::where('user_id',$user_id)
                                    ->where('type','<>','customer')->latest();
    
            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $notif = $data->limit($limit)->offset(($page - 1) * $limit);
            $data = $notif->get();
            $total = $notif->count();
            $data_new = [];
            foreach($data as $d => $val){
    
                $response_data = [
                    "id" => $val->id,
                    "read" => $val->read===0?false:true,
                    "title" => $val->title,
                    "type" => $val->type,
                    "image" => "",
                    "id_data" => $val->id_data,
                    "date" => $val->created_at,
                ];
    
                if($val->type==="service_info"){
                    
                    $extra = [
                        "service_status" => $val->service_status
                    ];
                    $response_data = array_merge($response_data, $extra);
    
                }elseif($val->type ==="review"){
                    
                    $review = ReviewService::find($val->id_data)->first();
                    if(is_null($review)){
                        $extra= [
                            "rating" => 0
                        ];
                    }else{
                        $extra= [
                            "rating" => (int)$review->ratings
                        ];
                    }
                    $response_data = array_merge($response_data, $extra);
    
                }elseif($val->type==="wallet"){
    
                    $withdraw = Withdraw::find($val->id_data);
                    if(is_null($withdraw)){
                        $extra['wallet']=[
                            "amount" => 0,
                            "message" => "",
                            "color" => ""
                        ];
                    }else{
                        $extra['wallet']=[
                            "amount" => (int)$withdraw->amount,
                            "message" => $val->subtitle??'',
                            "color" => $val->subtitle_color??''
                        ];
                    }
                    $response_data = array_merge($response_data, $extra);
    
                }elseif($val->type==="order"){
    
                    $order = Order::find($val->id_data);
                    if(is_null($order)){
                        $extra['order'] = [
                            "name" => null,
                            "total_service" => null
                        ];
                    }else{
                        if($order->order_type==="custom"){
                            $extra['order'] = [
                                "name" => "Custom Order",
                                "total_service" => 1
                            ];
                        }else{
                            $count = 0;
                            foreach ($order->order_detail as $key => $value) {
                                # code...
                                $count += $value->qty;
                            }
    
                            $extra['order'] = [
                                "name" => $order->order_detail[0]->name,
                                "total_service" => $count
                            ];
                        }
                    }
                    $response_data = array_merge($response_data, $extra);
                
                }
    
                $data_new[] = $response_data;
            }
    
            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data_new;
    
            return response()->json($response);        

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }

   
    }

    public function read(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required|integer',
        ]);

        if($validator->fails()){
            return response()->json(["message" => "Terjadi kesalhan ". $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $id = $request->notification_id;
            $notif = Notification::find($id);
            $notif->read = true;
            $notif->save();

            return response()->json(["message" => "Update success"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }
}
