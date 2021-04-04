<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\client;
use Carbon\Carbon;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //

        try {

            $data = Order::where('engineer_id',auth()->user()->id)->latest();

            // Filter Search
            $search = $request->get('query');
            $data->when($search, function ($query, $search) {
                return $query->whereHas('order_detail',function($query) use($search) {
                    $query->where('name', 'like', '%'.$search.'%');
                });
            });

            // Filter today,week,month
            $filter = $request->get('filter');
            $data->when($filter, function ($query, $filter) {
                if($filter==="today"){
                    return $query->whereDate('created_at', date('Y-m-d'));
                }elseif($filter === "week"){
                    return $query->whereBetween('created_at', [
                        Carbon::now()->startOfWeek(), 
                        Carbon::now()->endOfWeek()
                    ]);
                }elseif($filter==="month"){
                    return $query->whereYear('created_at', date('Y'))
                                    ->whereMonth('created_at', date('m'));
                }
            });

            $category = $request->get('category');
            $data->when($category, function ($query, $category) {
                return $query->whereIn('order_status', $category);
            });
            
            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $service = $data->limit($limit)->offset(($page - 1) * $limit);
            $data = $service->get();
            $total = $service->count();

            // dd($data[0]->order_detail[0]->name);
            
            $data_arr = [];
            foreach($data as $d => $value){
                $count = 0;
                foreach($value->order_detail as $d => $val){
                    $count += $val->qty;
                }
                $data_arr[] = [
                    "id" => $value->id,
                    "name" => $value->order_detail[0]->name,
                    "quantity" => $count,
                    "address" => json_decode($value->address)->name??'-',
                    "order_type" => $value->order_type,
                    "order_status" => $value->order_status,
                    "created_at" => $value->created_at
                ];
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = $total;
            $response['data'] = $data_arr;
    
            return response()->json($response);   

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        try {
            //code...

            $user = auth()->user();

            if(!($user->verified)){
                return response()->json(["message" => "Akun sedang diverifikasi"], 423);
            }

            $order = Order::find($id);

            $response = [
                "id" => $order->id,
                "order_status" => $order->order_status,
                "order_type" => $order->order_type,
                "is_take_away" => $order->is_take_away,
                "user" => [
                    "id" => $order->customer->id,
                    "name" => $order->customer->name,
                    "avatar" => $order->customer->profile_photo_path
                ],
                "review" => [
                    "value" => $order->review->ratings??null,
                    "liked" => []
                ],
                "address" => [
                    "latitude" => (float)json_decode($order->address)->lat,
                    "longitude" => (float)json_decode($order->address)->lng,
                    "description" => json_decode($order->address)->name,
                    "note" => ""
                ],
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function review(Request $request)
    {
        try {
            //code...
            $user = auth()->user();
            $data = Order::where('engineer_id',$user->id)
                            ->has('review');

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $order = $data->limit($limit)->offset(($page - 1) * $limit);
            $data = $order->get();
            $total = $order->count();

            $data_arr = [];
            foreach($data as $d => $value){

                $count = 0;
                foreach($value->order_detail as $d => $val){
                    $count += $val->qty;
                }

                $data_arr[] = [
                    "id" => $value->review->id,
                    "name" => $value->order_detail[0]->name,
                    "quantity" => $count,
                    "address" => json_decode($value->address)->name??'-',
                    "rating" => $value->review->ratings??null,
                    "created_at" => $value->review->created_at
                ];
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = $total;
            $response['data'] = $data_arr;
    
            return response()->json($response);   

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }
    
    public function order_accept($id){
        try {
            //code...
            $order = Order::find($id);
            $order->order_status = "waiting-order";
            $order->save();
            
            return response()->json(["message" => "Order Accepted"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_decline($id){
        try {
            //code...
            $order = Order::find($id);
            $order->order_status = "denied";
            $order->save();
            
            return response()->json(["message" => "Order Decline"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_process($id){
        try {
            //code...
            $order = Order::find($id);
            $order->order_status = "processed";
            $order->save();
            
            return response()->json(["message" => "Order Process"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_complete($id){
        try {
            //code...
            $order = Order::find($id);
            $order->order_status = "done";
            $order->save();
            
            return response()->json(["message" => "Order Comlpete"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_take_away($id){
        try {
            //code...
            $order = Order::find($id);
            $order->order_status = "take-away";
            $order->save();
            
            return response()->json(["message" => "Order Take Away"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }
}
