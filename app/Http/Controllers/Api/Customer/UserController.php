<?php

namespace App\Http\Controllers\Api\Customer;

use JWTAuth;
use App\Models\User;
use App\Mail\OtpMail;
use App\Models\Service;
use App\Models\Payment;
use App\Models\UserAddress;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\CategoryService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\OrderDetail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $otp = mt_rand(1000,9999);

        $cek = true;
        while ($cek) {
            # code...
            $userid = mt_rand(100000,999999);
            $cek_id = User::where('userid',$userid)->first();
            if(empty($cek_id)){
                $cek = false;
            }
        }

        try {
            //code...

            DB::beginTransaction();

            $user = User::create([
                'code_otp'          => $otp,
                'name'              => $request->get('name'),
                'email'             => $request->get('email'),
                "phone"             => $request->get('phone'),
                'userid'            => $userid,
                'password'          => Hash::make($request->get('password')),
            ]);

            $user->assignRole('user');
            
            $credentials = $request->only('email', 'password');
            $token = JWTAuth::attempt($credentials);
            
            $payload = JWTAuth::setToken($token)->getPayload();
            $expires_at = date('Y-m-d H:i:s', $payload->get('exp'));
            
            DB::commit();

            \Mail::to($request->get('email'))
                ->send(new \App\Mail\OtpMail($otp));
            
            $data['message'] = "Register berhasil";
            $data['token'] = $token;
            $data['token_type'] = "Bearer";
            $data['valid_until'] = $expires_at;

            return response()->json($data,200);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function login(Request $request)
    {
        if($request->has('id_google')){
            $userSocial = Socialite::driver('google')->stateless()->user();
            $users = User::where(['email' => $userSocial->getEmail()])->first();
            
            if($users){
                Auth::login($users);
                return "";
            }else{
                $user = User::create([
                    'name'          => $userSocial->getName(),
                    'email'         => $userSocial->getEmail(),
                    'image'         => $userSocial->getAvatar(),
                    'provider_id'   => $userSocial->getId(),
                    'provider'      => $provider,
                ]);
                return "";
            }
        }

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $credentials = $request->only('email', 'password');

        $user = User::where('email', $credentials['email'])->first();

        if(is_null($user)){
            return response()->json(['message' => 'Email belum terdaftar'], 423);
        }

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Opps... email atau kata sandi salah'], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 422);
        }

        if ($user && Hash::check($credentials['password'], $user->password)) {

            $payload = JWTAuth::setToken($token)->getPayload();
            $expires_at = date('Y-m-d H:i:s', $payload->get('exp'));
                
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

            $data['message'] = "Login successfully";            
            // $data['data'] = $user;
            $data['token'] = $token;
            $data['valid_until'] = $expires_at;
            $data['token_type'] = "Bearer";
            
            return response()->json($data);
        }else{
            return response()->json(['message' => 'Opps... email atau kata sandi salah'], 422);
        }        
    }


    public function request_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()->all()[0]], 422);
        }   

        try {

            $user = User::where('email',$request->get('email'))->first();

            if(is_null($user)){
                $message = 'Email tidak ditemukan';
                return response()->json(["message"=>$message], 422);
            }else{
                $newotp = mt_rand(1000,9999);
                $user->code_otp = $newotp;
                $user->save();
    
                \Mail::to($request->get('email'))
                    ->send(new \App\Mail\OtpMail($newotp));
                $message = "Kode Otp sudah dikirim ke email anda";
                return response()->json(["message"=>$message], 200);
            }

        } catch (\Throwable $th) {
                return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()], 422);
        }
    }

    public function confirmation_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code_otp' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()->all()[0]], 422);
        }

        $email = $request->get('email');
        $code_otp = $request->get('code_otp');

        try {
            $user = User::where('email',$email)->first();

            if(is_null($user)){
                // $message = 'Email tidak ditemukan';
                return response()->json(["message"=>"Email Tidak ditemukan"], 422);

            }else{
                
                if($user->code_otp===$code_otp){
                    $user->email_verified_at = date('Y-m-d H:i:s');
                    $user->save();
                    $message = 'konfirmasi kode otp berhasil';
                    return response()->json(compact('message'));
                }else{
                    $message = 'kode otp salah';
                    return response()->json(["message"=>$message], 423);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ". $th->getMessage()], 422);
        }
    }

    public function forgot_password_input_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code_otp' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()->all()[0]], 422);
        }

        $email = $request->get('email');
        $code_otp = $request->get('code_otp');

        try {
            //code...
            $user = User::where('email',$email)->first();
    
            if(is_null($user)){
                $message = 'Email tidak ditemukan';
                return response()->json(["message"=>$message], 422);
            }else{
                if($user->code_otp===$code_otp){
                    $message = 'konfirmasi kode otp berhasil';
                    $user->save();
                    $kode=200;
                }else{
                    $message = 'kode otp salah';
                    $kode=423;
                }
                return response()->json(['message'=>$message],$kode);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()], 422);
        }

    }

    public function show(){
        // dd('cek');
        try {
            //code...
            $user = auth()->user();

            $data = [
                "id" => $user->id,
                "name" => $user->name,
                "profil_photo" => $user->profil_photo_path?? asset('images/no_picture.jpg'),
                "phone" => $user->phone,
                "email" => $user->email
            ];

            return response()->json($data);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function update(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.auth()->user()->id,
            'phone' => 'required|unique:users,phone,'.auth()->user()->id,
        ]);
    
        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }
    
        try {
            //code...
            $user = auth()->user();
    
            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;
    
            $user->save();
    
            return response()->json(["message"=>"Data berhasil di update"]);
    
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }    
    }

    public function balance(Request $request)
    {
        try {
            //code...
            $user = auth()->user();
 
            $balance = (int)$user->balance;
    
            return response()->json(compact('balance'));
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function top_address()
    {
        try {
            //code...
            $data = UserAddress::where('user_id',auth()->user()->id)->latest()->limit(4);
            
            $data_arr = [];
            foreach($data->get() as $val){
                $data_arr[]=[
                    "id" => $val->id,
                    "name" => $val->name,
                    "address" => $val->address,
                    "description" => $val->note,
                    "geometry" => [
                        "lat" => $val->lat,
                        "lng" => $val->lng
                    ]
                ];
            }

            return response()->json($data_arr);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function service_category()
    {
        try {
            //code...
            $category_service = CategoryService::where('status',1)->get();

            $data = [];
            foreach($category_service as $val){
                $data_temp = [
                    "id" => (int)$val->id,
                    "slug" => $val->slug,
                    "label" => $val->name,
                    "icon" => $val->icon??""
                ];
                $data[] = $data_temp;
            }

            return response()->json($data);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function service_recommendation(Request $request)
    {
        try {

            $service = Service::latest();

            if($request->has('query')){
                $query = $request->get('query');
                $service->where('name', 'like', '%'.$query.'%');
            }
    
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
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function service(Request $request)
    {
        try {

            $q = $request->get('query');
            $category = $request->get('category');
            $sorting = $request->get('sorting');
            
            $service = Service::when($q, function ($query, $q) {
                                        return $query->where('name', 'like', '%'.$q.'%');
                                })
                                ->when($category, function ($query, $category) {
                                    return $query->whereHas('service_category', function ($query) use ($category) {
                                        $query->where('slug', $category);
                                    });
                                })
                                ->when($sorting, function ($query, $sorting) {
                                    if($sorting=="price_asc"){
                                        return $query->orderBy('price','asc');
                                    }else{
                                        return $query->orderBy('price','desc');
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
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }        
    }

    public function service_detail($id){
        try {
            //code...
            $service = Service::find($id);

            $data = [
                "id" => (int)$service->id,
                "name" => $service->name,
                "media" => $service->image,
                "price" => (int)$service->price,
                "guarantee" => 3,
                "weight" => 450,
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
            return response()->json("Terjadi kesalahan ".$th->getMessage());
        }
    }

    public function order_generate_payment(Request $request)
    {
        try {
            //code...
            DB::beginTransaction();

            $lat = $request->get('lat');
            $lng = $request->get('lng');
            $notes = $request->get('notes');
            $services = $request->get('services');
            
            if($request->has('address_id')){
                $address_id = $request->get('address_id');
                $user_address = UserAddress::find($address_id);

                $address = [
                    "name" => $user_address->address,
                    "lat" => $user_address->lat,
                    "lng" => $user_address->lng,
                    "notes" => $user_address->note
                ];
            }else{
                $address = [
                    "name" => "custom",
                    "lat" => (float)$lat,
                    "lng" => (float)$lng,
                    "notes" => $notes
                ];
            }

            $total_service_price = 0;
            foreach ($services as $key => $value) {
                $service_id = $value['service_id'];
                $qty = $value['quantity'];
                
                $service = Service::find($service_id);
                $total_service_price += $service->price * $qty;
            }

            $shipping = 12000;
            $unique_code = mt_rand(100,999);

            $total_price = $total_service_price+$shipping+$unique_code;

            if($request->has('promo_code')){
                $promo_code = $request->get('promo_code');
                $promo = 12000;
                $promo_message = [
                    "message" => "Kodo promo aktif",
                    "positive" => true
                ];
            }else{
                $promo = 0;
                $promo_message = [
                    "message" => "",
                    "positive" => ""
                ];
            }

            Payment::create([
                "customer_id" => auth()->user()->id,
                "amount" => $total_price,
                "type" => "reguler",
                "paymentid" => "P".uniqid(),
                "convenience_fee" => $unique_code,
                "orders" =>[]
            ]);

            $response = [
                "total_service_price" => $total_service_price,
                "price_distance" => $shipping,
                "unique_code" => $unique_code,
                "total_price" => $total_price,
                "promo" => $promo,
                "promo_message" => $promo_message
            ];
            
            DB::commit();

            return response()->json($response);


        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function order_checkout(Request $request){
        try {

            DB::beginTransaction();

            $lat = $request->get('lat');
            $lng = $request->get('lng');
            $notes = $request->get('notes');
            $services = $request->get('services');
            
            if($request->has('address_id')){
                $address_id = $request->get('address_id');
                $user_address = UserAddress::find($address_id);

                $address = [
                    "name" => $user_address->address,
                    "lat" => $user_address->lat,
                    "lng" => $user_address->lng,
                    "notes" => $user_address->note
                ];
            }else{
                $address = [
                    "name" => "custom",
                    "lat" => (float)$lat,
                    "lng" => (float)$lng,
                    "notes" => $notes
                ];
            }

            $total_service_price = 0;
            $engineer_id = [];

            foreach ($services as $key => $value) {
                $service_id = $value['service_id'];
                $qty = $value['quantity'];
                
                $service = Service::find($service_id);
                $total_service_price += $service->price * $qty;
                $engineer_id[] = $service->engineer_id;
            }

            $shipping = 12000;
            $unique_code = mt_rand(100,999);

            $total_price = $total_service_price+$shipping+$unique_code;

            $order = Order::create([
                "order_number" => uniqid(),
                "customer_id" => auth()->user()->id,
                "engineer_id" => $engineer_id[0],
                "shipping" => $shipping,
                "convenience_fee" => $unique_code,
                "total_payment" => $total_price,
                "total_payment_receive" => $total_price,
                "address" => json_encode($address)
            ]);

            if($request->has('promo_code')){
                $promo_code = $request->get('promo_code');
                $order->promo_code = $promo_code;
                $order->save();
                $promo = 12000;
                $promo_message = [
                    "message" => "Kodo promo aktif",
                    "positive" => true
                ];
            }else{
                $promo = 0;
                $promo_message = [
                    "message" => "",
                    "positive" => ""
                ];
            }

            foreach ($services as $key => $value) {
                $service_id = $value['service_id'];
                $qty = $value['quantity'];

                $service = Service::find($service_id);
                OrderDetail::create([
                    "order_id" => $order->id,
                    "name" => $service->name,
                    "qty" => $qty,
                    "price" => $service->price
                ]);
            }
            
            $order_detail = OrderDetail::where('order_id',$order->id)->get();
            $orderdetail_data = [];
            foreach ($order_detail as $key => $value) {
                $orderdetail_data[] = [
                    "service_id" => "",
                    "name" => $value->name,
                    "quantity" => (int)$value->qty,
                    "price" => $value->price
                ];
            }
            
            $response = [
                "order_id" => $order->order_number,
                "expired_date" => $order->created_at->addHour(),
                "services" => $orderdetail_data,
                "price_distance" => $shipping,
                "unique_code" => $unique_code,
                "total_price" => $total_price,
            ];
            DB::commit();
            
            return response()->json($response);
            
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }
    
    public function order($order_id)
    {
        try {
            $order = Order::where('order_number',$order_id)->first();
            $orderDetail = [];
            foreach ($order->order_detail as $key => $value) {
                $orderDetail[] = [
                    "service_id" => "",
                    "name" => $value->name,
                    "quantity" => (int)$value->qty,
                    "price" => (int)$value->qty * $value->price
                ];
            }

            $response = [
                "order_id" => $order->order_number,
                "expired_date" => $order->created_at->addHour(),
                "services" => $orderDetail,
                "price_distance" => (int)$order->shipping,
                "unique_code" => (int)$order->convenience_fee,
                "total_price" => (int)$order->total_payment,
            ];

            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }
}
