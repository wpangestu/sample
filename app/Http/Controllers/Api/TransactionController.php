<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use App\Models\Order;
use App\Models\client;
use App\Models\Chatroom;
use App\Models\BaseService;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

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
                    "id" => $value->order_number,
                    "name" => $value->order_detail[0]->name,
                    "quantity" => $count,
                    "address" => json_decode($value->address)->description??'-',
                    "order_type" => $value->order_type,
                    "order_status" => $value->order_status,
                    "created_at" => $value->created_at
                ];
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
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

            $order = Order::where('order_number',$id)->first();

            $response = [
                "id" => $order->order_number,
                "order_status" => $order->order_status,
                "order_type" => $order->order_type,
                "is_extend" => $order->is_extend?true:false,
                "user" => [
                    "id" => (int)$order->customer->id,
                    "name" => $order->customer->name,
                    "avatar" => $order->customer->profile_photo_path??""
                ],
                "review" => [
                    "value" => $order->review->ratings??0,
                    "liked" => $order->review->liked??[]
                ],
                "address" => [
                    "latitude" => (float)json_decode($order->address)->latitude??0,
                    "longitude" => (float)json_decode($order->address)->longitude??0,
                    "description" => json_decode($order->address)->description??"",
                    "note" => json_decode($order->address)->notes??""
                ],
                "created_at" => $order->created_at,
            ];

            $detail=[];
            if($order->order_type==="regular"){
                foreach($order->order_detail as $val){

                    $detail["order"][] = [
                        "id" => (int)$val->id,
                        "name" => $val->name,
                        "quantity" => (int)$val->qty,
                        "price" => (int)$val->price
                    ];
                }
                $combined = array_merge($response, $detail);

                $extra = [
                    "convenience_fee" => (int)$order->convenience_fee??0,
                    "total_payment" => (int)$order->total_payment??0,
                    "total_payment_receive" => (int)$order->total_payment_receive??0
                ];

                $combined = array_merge($combined,$extra);
            }elseif($order->order_type==="custom"){
                $custom["custom_order"] = [
                    "level" => json_decode($order->custom_order)->level??"",
                    "brand" => json_decode($order->custom_order)->brand??"",
                    "custom_type" => json_decode($order->custom_order)->custom_type??"",
                    "information" => json_decode($order->custom_order)->information??"",
                    "problem_details" => json_decode($order->custom_order)->problem_details??""
                ];

                $combined = array_merge($response, $custom);

                $extra = [
                    "deposit" => (int)$order->deposit
                ];

                $combined = array_merge($combined,$extra);
            }

            return response()->json($combined);

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
                    "id" => (int)$value->review->id,
                    "name" => $value->order_detail[0]->name,
                    "quantity" => (int)$count,
                    "address" => json_decode($value->address)->description??'-',
                    "rating" => $value->review->ratings??0,
                    "created_at" => $value->review->created_at
                ];
            }

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $data_arr;
    
            return response()->json($response);   

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }
    
    public function order_accept(Request $request, $id){
        try {
            //code...
            DB::beginTransaction();

            $lat = 0;
            $lng = 0;
            $description = "Alamat teknisi";

            if($request->has('lat')){
                $lat = $request->get('lat');
            }
            if($request->has('lng')){
                $lng = $request->get('lng');
            }

            $key = env('GOOGLE_API_KEY','');

            $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json",[
                "latlng" => $lat.",".$lng,
                "language" => "id",
                "key" => $key,
            ]);

            if($response->successful()){
                $result = $response->json()['results'][0];
            }else{
                $errors = json_decode($response->getBody()->getContents());
                return response()->json(["message" => "Terjadi Kesalahan ".$errors],422);                
            }

            $origin = [
                "description" => $result['formatted_address']??$description,
                "latitude" => (float)$lat,
                "longitude" => (float)$lng,
            ];

            $order = Order::where('order_number',$id)->first();
            if(!is_null($order->engineer_id)){
                return response()->json(["message" => "Pesanan sudah ada yang mengambil"], 422);                
            }

            $order->order_status = "accepted";
            $order->engineer_id = auth()->user()->id;
            $order->origin = json_encode($origin);
            $order->save();

            $chatroom = Chatroom::where('user_1',$order->customer_id)
                                    ->where('user_2',$order->engineer_id)
                                    ->where('open',1);
            if($chatroom->count() == 0){
                $chatroom = Chatroom::where('user_1',$order->engineer_id)
                                        ->where('user_2',$order->customer_id)
                                        ->where('open',1);
            }

            if($chatroom->count() > 0){
                $chatroom = $chatroom->first();
            }else{
                $chatroom = Chatroom::create([
                    "user_1" => $order->engineer_id,
                    "user_2" => $order->customer_id
                ]);
            }

            $chat = Chat::create([
                "to" => $order->customer_id,
                "from" => $order->engineer_id,
                "message" => "Perkenalkan Saya ".$order->engineer->name.", saya teknisi yang akan menangani orderan anda.",
                "chatroom_id" => $chatroom->id,
                "media" => null
            ]);

            DB::commit();
            
            return response()->json(["message" => "Order Accepted"]);            
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_decline($id){
        try {
            //code...
            // $order = Order::find($id);
            // $order->order_status = "denied";
            // $order->save();
            
            return response()->json(["message" => "Order Decline"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_process($id){
        try {
            //code...
            $order = Order::where('order_number',$id)->first();
            $order->order_status = "processed";
            $order->save();
            
            return response()->json(["message" => "Order Process"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_complete(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'photo' => 'required|mimes:img,png,jpeg,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $uploadFolder = 'teknisi/order';
            $photo = $request->file('photo');
            $photo_path = $photo->store($uploadFolder,'public');

            DB::beginTransaction();

            $order = Order::where('order_number',$id)->first();
            $order->order_status = "done";
            $order->photo = Storage::disk('public')->url($photo_path);
            $order->save();

            $user = User::find($order->engineer_id);
            $fee_technician = 0;
            if($order->order_type=="regular"){
                foreach ($order->order_detail as $key => $value) {
                    # code...
                    $base = BaseService::find($value->base_id);
                    $fee_technician += $base->price_receive;
                }
            }

            $user->balance = $user->balance+$fee_technician;
            $user->save();

            $title = "Order #".$order->order_number." telah selesai";
            $subtitle = "Saldo ".rupiah($fee_technician)." telah masuk ke saldo anda";
            Notification::create([
                "title" => $title,
                "type" => "order",
                "user_id" => $order->engineer_id,
                "read" => false,
                "subtitle" => $subtitle,
                "id_data" => $order->order_number,
            ]);

            $token[] = $order->engineer->fcm_token;
            fcm()->to($token)
                    ->priority('high')
                    ->timeToLive(0)
                    ->notification([
                        'title' => $title,
                        'body' => $subtitle,
                    ]);            

            DB::commit();

            return response()->json(["message" => "Order Comlpete"]);            
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }

    public function order_extend($id){
        try {
            //code...
            $order = Order::where('order_number',$id)->first();
            $order->order_status = "extend";
            $order->is_extend = true;
            $order->save();
            
            return response()->json(["message" => "Order Extend"]);            
            
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()], 422);
            //throw $th;
        }
    }
}
