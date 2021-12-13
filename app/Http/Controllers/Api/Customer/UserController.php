<?php

namespace App\Http\Controllers\Api\Customer;

use JWTAuth;
use Carbon\Carbon;
use App\Models\Bank;
use App\Models\User;
use App\Mail\OtpMail;
use App\Models\Order;
use App\Models\Promo;
use App\Models\Deposit;
use App\Models\Payment;
use App\Models\Service;
use App\Models\Withdraw;
use App\Models\BaseService;
use App\Models\OrderDetail;
use App\Models\UserAddress;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Models\ReviewService;
use App\Models\CategoryService;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\Eloquent\Builder;

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
            'id_google' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $otp = mt_rand(1000, 9999);

        $cek = true;
        while ($cek) {
            # code...
            $userid = mt_rand(100000, 999999);
            $cek_id = User::where('userid', $userid)->first();
            if (empty($cek_id)) {
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
                'id_google'         => $request->get('id_google') ?? null
            ]);

            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

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

            return response()->json($data, 200);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255',
            'device_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $email_check = User::where('email', $request->get('email'))->first();

        if ($request->has('id_google')) {

            $id_google = $request->get('id_google');
            $email = $request->get('email');

            $user = User::where('id_google', $id_google)->where('email', $email)->first();
            if (is_null($user) && is_null($email_check)) {
                return response()->json(["message" => "Akun belum terdaftar"], 424);
            } elseif (is_null($user)) {
                return response()->json(["message" => "Akun tidak ditemukan"], 425);
            } else {

                try {
                    $token = JWTAuth::fromUser($user);
                } catch (JWTException $e) {
                    return response()->json(['error' => 'could_not_create_token'], 422);
                }

                $payload = JWTAuth::setToken($token)->getPayload();
                $expires_at = date('Y-m-d H:i:s', $payload->get('exp'));

                $user->last_login = date('Y-m-d H:i:s');
                if(is_null($user->device_id)){
                    $user->device_id = $request->device_id;
                }
                $user->save();

                $data['message'] = "Login successfully";
                // $data['data'] = $user;
                $data['token'] = $token;
                $data['valid_until'] = $expires_at;
                $data['token_type'] = "Bearer";

                return response()->json($data);
            }
        } else {

            if (is_null($email_check)) {
                return response()->json(['message' => 'Email belum terdaftar'], 423);
            }

            $validator = Validator::make($request->all(), [
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(["message" => $validator->errors()->all()[0]], 422);
            }
            $credentials = $request->only('email', 'password');
        }

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['message' => 'Opps... email atau kata sandi salah'], 422);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 422);
        }

        $user = $email_check;

        if ($user && Hash::check($credentials['password'], $user->password)) {

            if ($user->hasRole('teknisi')) {
                return response()->json(['message' => 'Akun anda terdaftar sebagai akun teknisi, gunakan aplikasi Benerin teknisi'], 422);
            }
            if ($user->hasAnyRole(['admin', 'superadmin', 'cs'])) {
                return response()->json(['message' => 'Akun anda tidak bisa login'], 422);
            }

            $payload = JWTAuth::setToken($token)->getPayload();
            $expires_at = date('Y-m-d H:i:s', $payload->get('exp'));

            $user->last_login = date('Y-m-d H:i:s');
            if(is_null($user->device_id)){
                $user->device_id = $request->device_id;
            }
            $user->save();

            $data['message'] = "Login successfully";
            // $data['data'] = $user;
            $data['token'] = $token;
            $data['valid_until'] = $expires_at;
            $data['token_type'] = "Bearer";

            return response()->json($data);
        } else {
            return response()->json(['message' => 'Opps... email atau kata sandi salah'], 422);
        }
    }

    public function check_valid(Request $request)
    {   
        try{            
            $device_id = $request->device_id;
            if($device_id === auth()->user()->device_id){
                return response()->json(['message' => 'Device id correct'], 200);
            }else{
                return response()->json(['message' => 'You have logged in other device'], 422);
            }
        
        } catch (\Throwable $th) {
                return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }


    public function request_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {

            $user = User::where('email', $request->get('email'))->first();

            if (is_null($user)) {
                $message = 'Email tidak ditemukan';
                return response()->json(["message" => $message], 422);
            } else {
                $newotp = mt_rand(1000, 9999);
                $user->code_otp = $newotp;
                $user->save();

                \Mail::to($request->get('email'))
                    ->send(new \App\Mail\OtpMail($newotp));
                $message = "Kode Otp sudah dikirim ke email anda";
                return response()->json(["message" => $message], 200);
            }
        } catch (\Throwable $th) {
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function confirmation_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code_otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $email = $request->get('email');
        $code_otp = $request->get('code_otp');

        try {
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                // $message = 'Email tidak ditemukan';
                return response()->json(["message" => "Email Tidak ditemukan"], 422);
            } else {

                if ($user->code_otp === $code_otp) {
                    $user->email_verified_at = date('Y-m-d H:i:s');
                    $user->save();
                    $message = 'konfirmasi kode otp berhasil';
                    return response()->json(compact('message'));
                } else {
                    $message = 'kode otp salah';
                    return response()->json(["message" => $message], 423);
                }
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function forgot_password_input_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code_otp' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $email = $request->get('email');
        $code_otp = $request->get('code_otp');

        try {
            //code...
            $user = User::where('email', $email)->first();

            if (is_null($user)) {
                $message = 'Email tidak ditemukan';
                return response()->json(["message" => $message], 422);
            } else {
                if ($user->code_otp === $code_otp) {
                    $message = 'konfirmasi kode otp berhasil';
                    $user->save();
                    $kode = 200;
                } else {
                    $message = 'kode otp salah';
                    $kode = 423;
                }
                return response()->json(['message' => $message], $kode);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function show()
    {
        // dd('cek');
        try {
            //code...
            $user = auth()->user();

            $response = [
                "id" => $user->id,
                "name" => $user->name,
                "profile_photo" => $user->profil_photo_path ?? "",
                "phone" => $user->phone,
                "email" => $user->email
            ];

            // return response()->json($data);
            // $response->header('Content-Type', 'application/json');

            // return $response;

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . auth()->user()->id,
            'phone' => 'required|unique:users,phone,' . auth()->user()->id,
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $user = auth()->user();

            $user->name = $request->name;
            $user->phone = $request->phone;
            $user->email = $request->email;

            $user->save();

            return response()->json(["message" => "Data berhasil di update"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
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
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function top_address()
    {
        try {
            //code...
            $data = UserAddress::where('user_id', auth()->user()->id)->latest()->limit(4);

            $data_arr = [];
            foreach ($data->get() as $val) {
                $data_arr[] = [
                    "id" => $val->id,
                    "name" => $val->name,
                    "address" => $val->address,
                    "description" => $val->note,
                    "geometry" => [
                        "lat" => (float)$val->lat,
                        "lng" => (float)$val->lng
                    ]
                ];
            }

            return response()->json($data_arr);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function service_category()
    {
        try {
            //code...
            $category_service = CategoryService::where('status', 1)->get();

            $data = [];
            foreach ($category_service as $val) {
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
            // throw $th;
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
                # code...
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

    public function order_generate_payment(Request $request)
    {
        try {
            //code...
            DB::beginTransaction();

            $lat = $request->get('lat');
            $lng = $request->get('lng');
            $notes = $request->get('notes');
            $services = $request->get('services');
            $custom_category = $request->get('custom_category');
            $custom_type = $request->get('custom_type');

            if ($request->has('address_id')) {
                $address_id = $request->get('address_id');
                $user_address = UserAddress::find($address_id);

                $address = [
                    "name" => $user_address->address,
                    "lat" => (float)$user_address->lat,
                    "lng" => (float)$user_address->lng,
                    "notes" => $user_address->note
                ];
            } else {
                $address = [
                    "name" => "custom",
                    "lat" => (float)$lat,
                    "lng" => (float)$lng,
                    "notes" => $notes
                ];
            }

            $total_service_price = 0;

            if (isset($custom_category)) {
                $service = BaseService::find($custom_category);
                $total_service_price = $service->price ?? 0;
            } elseif (isset($services)) {
                foreach ($services as $key => $value) {
                    $service_id = $value['service_id'];
                    $qty = $value['quantity'];

                    $service = BaseService::find($service_id);
                    $total_service_price += $service->price * $qty;
                }
            }

            $shipping = 0;
            $unique_code = mt_rand(100, 999);

            $total_price = $total_service_price + $shipping + $unique_code;

            if ($request->has('promo_code')) {
                $promo_code = $request->get('promo_code');
                $promo = Promo::where('code', $promo_code)
                    ->where('is_active', 1)
                    ->first();

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

            // Payment::create([
            //     "customer_id" => auth()->user()->id,
            //     "amount" => $total_price,
            //     "type" => "reguler",
            //     "paymentid" => "P" . uniqid(),
            //     "convenience_fee" => $unique_code,
            //     "orders" => []
            // ]);

            $response = [
                "total_service_price" => (int)$total_service_price,
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

    public function order_checkout(Request $request)
    {
        try {

            DB::beginTransaction();

            $lat = $request->get('lat');
            $lng = $request->get('lng');
            $notes = $request->get('notes');
            $services = $request->get('services');
            $payment_type = $request->get('payment_type');

            $address = [];

            if ($request->has('address_id')) {
                $address_id = $request->get('address_id');
                $user_address = UserAddress::find($address_id);

                $address = [
                    "description" => $user_address->address,
                    "latitude" => (float)$user_address->lat,
                    "longitude" => (float)$user_address->lng,
                    "notes" => $user_address->note
                ];
            } else {

                $key = env('GOOGLE_API_KEY', '');

                $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                    "latlng" => $request->lat . "," . $request->lng,
                    "language" => "id",
                    "key" => $key,
                ]);

                if ($response->successful()) {
                    $result = $response->json()['results'][0];
                } else {
                    $errors = json_decode($response->getBody()->getContents());
                    return response()->json(["message" => "Terjadi Kesalahan " . $errors], 422);
                }

                $address = [
                    "description" => $result['formatted_address'] ?? '',
                    "latitude" => (float)$lat,
                    "longitude" => (float)$lng,
                    "notes" => $notes
                ];
            }

            $customer = auth()->user();
            $customer->address = $address['description'];
            $customer->lat = $address['latitude'];
            $customer->lng = $address['longitude'];
            $customer->save();

            $total_service_price = 0;
            $total_payment_reveice = 0;
            $engineer_id = [];

            foreach ($services as $key => $value) {
                $service_id = $value['service_id'];
                $qty = $value['quantity'];

                $service = BaseService::find($service_id);
                $total_service_price += $service->price * $qty;
                $total_payment_reveice += $service->price_receive * $qty;
                $engineer_id[] = $service->engineer_id;
            }

            $shipping = 0;
            $unique_code = mt_rand(100, 999);
            
            if ($payment_type == "saldo") {
                $unique_code = 0;
            }

            $total_price = $total_service_price + $shipping + $unique_code;

            $order = Order::create([
                "order_number" => "O" . uniqid(),
                "customer_id" => auth()->user()->id,
                "engineer_id" => $engineer_id[0],
                "shipping" => $shipping,
                "convenience_fee" => $unique_code,
                "total_payment" => $total_price,
                "total_payment_receive" => $total_payment_reveice,
                "address" => json_encode($address),
                "expired_date" => Carbon::now()->addHour()
            ]);



            if ($payment_type == "saldo") {

                if ($customer->balance >= $total_price) {
                    $order->order_status = "payment_success";
                    $order->save();

                    $customer->balance -= $total_price;
                    $customer->save();

                    Payment::create([
                        "customer_id" => $customer->id,
                        "amount" => $total_service_price,
                        "paymentid" => "P" . uniqid(),
                        "status" => "success",
                        "type" => "saldo",
                        "orders" => $order->order_number,
                        "data_id" => $order->order_number,
                        "payment_method" => "saldo"
                    ]);

                } else {
                    return response()->json(["message" => "Maaf saldo anda tidak mencukupi"], 422);
                }
            }

            $promo_data = [];
            $promo_value = 0;
            if ($request->has('promo_code')) {
                $promo_code = $request->get('promo_code');

                $promo = Promo::where('code', $promo_code)
                    ->where('is_active', 1)
                    ->first();

                if (!is_null($promo)) {
                    $promo_value = $promo->value;
                    $promo_data = [
                        "code_promo" => $promo->code,
                        "value" => (int)$promo->value
                    ];
                }
            }

            $order->promo_code = json_encode($promo_data);
            $order->total_payment = $total_price - $promo_value;
            // $order->total_payment_receive = $total_price-$promo_value;
            $order->save();

            foreach ($services as $key => $value) {
                $service_id = $value['service_id'];
                $qty = $value['quantity'];

                $service = BaseService::find($service_id);
                OrderDetail::create([
                    "order_id" => $order->id,
                    "name" => $service->name,
                    "qty" => $qty,
                    "price" => $service->price,
                    "base_id" => $service->id,
                    "image" => $service->image ?? '',
                    'price_receive' => $service->price_receive
                ]);
            }

            $order_detail = OrderDetail::where('order_id', $order->id)->get();
            $orderdetail_data = [];
            $no = 1;
            foreach ($order_detail as $key => $value) {
                $orderdetail_data[] = [
                    "service_id" => $no++,
                    "name" => $value->name,
                    "quantity" => (int)$value->qty,
                    "price" => (int)$value->price
                ];
            }

            $engineer = User::find($engineer_id[0]);
            $engineer_data = null;
            $origin = null;
            if (!is_null($engineer)) {
                $engineer_data = [
                    "technician_id" => (int)$engineer->userid ?? 0,
                    "name" => $engineer->name ?? '',
                    "media" => $engineer->profile_photo_path ?? '',
                    "rating" => (float)$engineer->rating??0
                ];

                $origin = [
                    "latitude" => (float)json_decode($order->origin)->latitude ?? 0,
                    "longitude" => (float)json_decode($order->origin)->longitude ?? 0,
                    "description" => json_decode($order->irigin)->description ?? ''
                ];
            }
            // dd($engineer);

            $destination = [
                "latitude" => (float)$address['latitude'] ?? 0,
                "longitude" => (float)$address['longitude'] ?? 0,
                "description" => $address['description'] ?? '',
                "note" => $address['notes'] ?? ''
            ];

            $review = [
                "value" => 0,
                "liked" => []
            ];

            $order_data = Order::find($order->id);

            $response = [
                "order_id" => $order->order_number,
                "expired_date" => $order->expired_date,
                "order_status" => $order_data->order_status,
                "technician" => $engineer_data,
                "destination" => $destination,
                "origin" => $origin,
                "services" => $orderdetail_data,
                "review" => $review,
                "price_distance" => $shipping,
                "promo" => (int)$promo_value,
                "unique_code" => $unique_code,
                "total_price" => $total_price,
            ];
            DB::commit();

            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function custom_order_checkout(Request $request)
    {
        try {

            DB::beginTransaction();

            $lat = $request->get('lat');
            $lng = $request->get('lng');
            $notes = $request->get('notes');
            $service = $request->get('category');
            $custom_type = $request->get('custom_type');
            $brand = $request->get('brand');
            $custom_info = $request->get('custom_info');
            $detail_info = $request->get('detail_info');

            $address = [];

            if ($request->has('address_id')) {
                $address_id = $request->get('address_id');
                $user_address = UserAddress::find($address_id);

                $address = [
                    "description" => $user_address->address,
                    "latitude" => (float)$user_address->lat,
                    "longitude" => (float)$user_address->lng,
                    "notes" => $user_address->note
                ];
            } else {

                $key = env('GOOGLE_API_KEY', '');

                $response = Http::get("https://maps.googleapis.com/maps/api/geocode/json", [
                    "latlng" => $request->lat . "," . $request->lng,
                    "language" => "id",
                    "key" => $key,
                ]);

                if ($response->successful()) {
                    $result = $response->json()['results'][0];
                } else {
                    $errors = json_decode($response->getBody()->getContents());
                    return response()->json(["message" => "Terjadi Kesalahan " . $errors], 422);
                }

                $address = [
                    "description" => $result['formatted_address'] ?? '',
                    "latitude" => (float)$lat,
                    "longitude" => (float)$lng,
                    "notes" => $notes
                ];
            }

            $customer = auth()->user();
            $customer->address = $address['description'];
            $customer->lat = $address['latitude'];
            $customer->lng = $address['longitude'];
            $customer->save();

            $total_service_price = 0;
            $service = BaseService::find($service);

            $custom_order_data = [
                "level" => $custom_type,
                "custom_type" => $service->name ?? '-',
                "brand" => $brand,
                "information" => $custom_info,
                "problem_details" => $detail_info
            ];

            // dd($service);
            if (!is_null($service)) {
                $total_service_price = $service->price;
            }

            $shipping = 0;
            $unique_code = mt_rand(100, 999);

            $total_payment_receive = $service->price_receive;
            $total_price = $total_service_price + $shipping + $unique_code;

            $order = Order::create([
                "order_number" => "CO" . uniqid(),
                "order_type" => "custom",
                "customer_id" => auth()->user()->id,
                "engineer_id" => null,
                "shipping" => $shipping,
                "convenience_fee" => $unique_code,
                "total_payment" => $total_price,
                "total_payment_receive" => $total_payment_receive,
                "address" => json_encode($address),
                "custom_order" => null,
                "order_status" => "waiting_payment",
                "expired_date" => Carbon::now()->addHour()
            ]);

            OrderDetail::create([
                "order_id" => $order->id,
                "name" => $service->name,
                "qty" => 1,
                "price" => $service->price,
                "base_id" => $service->id,
                "image" => $service->image ?? '',
                'price_receive' => $service->price_receive,
                'custom_order' => json_encode($custom_order_data)
            ]);

            $payment_type = $request->get('payment_type');

            if ($payment_type == "saldo") {

                return response()->json(["message" => "Maaf transaksi ini tidak bisa menggunakan saldo"], 422);

                // if ($customer->balance >= $total_price) {
                //     $order->order_status = "payment_success";
                //     $order->save();
                //     $customer->balance -= $total_price;
                //     $customer->save();
                // } else {
                //     return response()->json(["message" => "Maaf saldo anda tidak mencukupi"], 422);
                // }
            }

            $engineer_data = null;
            $origin = null;

            // dd($engineer);

            $destination = [
                "latitude" => (float)$address['latitude'] ?? 0,
                "longitude" => (float)$address['longitude'] ?? 0,
                "description" => $address['description'] ?? '',
                "note" => $address['notes'] ?? ''
            ];

            $review = [
                "value" => 0,
                "liked" => []
            ];

            // $order_data = Order::find($order->id);

            $response = [
                "order_id" => $order->order_number,
                "expired_date" => $order->expired_date,
                "order_status" => $order->order_status,
                "technician" => $engineer_data,
                "destination" => $destination,
                "origin" => $origin,
                "review" => $review,
                "custom_order" => $custom_order_data,
                "price_custom" => (int)$total_service_price,
                "price_distance" => $shipping,
                "unique_code" => $unique_code,
                "total_price" => $total_price,
            ];
            DB::commit();

            return response()->json($response);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function order($order_id)
    {
        try {
            $order = Order::where('order_number', $order_id)->first();

            $destination = [
                "latitude" => (float)json_decode($order->address)->latitude ?? 0,
                "longitude" => (float)json_decode($order->address)->longitude ?? 0,
                "description" => json_decode($order->address)->description ?? '',
                "note" => json_decode($order->address)->notes ?? '',
            ];
 
            $technician = null;
            if (isset($order->engineer)) {
                $technician = [
                    "technician_id" => (int)$order->engineer->id,
                    "name" => $order->engineer->name,
                    "media" => $order->engineer->profile_photo_path ?? '',
                    "rating" => (float)$order->engineer->rating??0
                ];
            }

            $origin = null;
            if (isset($order->origin)) {
                $origin = [
                    "latitude" => (float)json_decode($order->origin)->latitude ?? 0,
                    "longitude" => (float)json_decode($order->origin)->longitude ?? 0,
                    "description" => json_decode($order->origin)->description ?? ''
                ];
            }

            $review = [
                "value" => 0,
                "liked" => [],
                "review_reason" => ""
            ];
            $reviewData = ReviewService::where('order_number_id', $order->order_number)->first();
            if (isset($reviewData)) {
                $review = [
                    "value" => (float)$reviewData->ratings ?? 0,
                    "liked" => $reviewData->liked??[],
                    "review_reason" => $reviewData->description??""
                ];
            }

            // $response=null;
            if ($order->order_type == "regular") {
                $orderDetail = [];
                $no = 1;
                foreach ($order->order_detail as $key => $value) {
                    $orderDetail[] = [
                        "service_id" => $no++,
                        "name" => $value->name,
                        "quantity" => (int)$value->qty,
                        "price" => (int)$value->qty * $value->price
                    ];
                }

                $response = [
                    "order_id" => $order->order_number,
                    "expired_date" => $order->expired_date ?? $order->created_at->addHour(),
                    "order_status" => $order->order_status,
                    "technician" => $technician,
                    "destination" => $destination,
                    "origin" => $origin,
                    "services" => $orderDetail,
                    "review" => $review,
                    "price_distance" => (int)$order->shipping,
                    "promo" => json_decode($order->promo_code)->value ?? 0,
                    "unique_code" => (int)$order->convenience_fee,
                    "total_price" => (int)$order->total_payment,
                    "created_at" => $order->created_at
                ];
                return response()->json($response);
            }

            $customorder = null;
            if ($order->order_type == "custom") {
                $customorder = [
                    "level" => json_decode($order->order_detail[0]->custom_order)->level ?? '',
                    "custom_type" => json_decode($order->order_detail[0]->custom_order)->custom_type ?? '',
                    "brand" => json_decode($order->order_detail[0]->custom_order)->brand ?? '',
                    "information" => json_decode($order->order_detail[0]->custom_order)->information ?? '',
                    "problem_details" => json_decode($order->order_detail[0]->custom_order)->problem_details ?? ''
                ];

                $response = [
                    "order_id" => $order->order_number,
                    "expired_date" => $order->expired_date ?? $order->created_at->addHour(),
                    "order_status" => $order->order_status,
                    "technician" => $technician,
                    "destination" => $destination,
                    "origin" => $origin,
                    "custom_order" => $customorder,
                    "review" => $review,
                    "price_distance" => (int)$order->shipping,
                    "unique_code" => (int)$order->convenience_fee,
                    "price_custom" => (int)$order->total_payment - (int)$order->convenience_fee,
                    "total_price" => (int)$order->total_payment,
                    "created_at" => $order->created_at
                ];
                return response()->json($response);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function cancel_order($order_id)
    {
        try {
            //code...
            $order = Order::where('order_number', $order_id)->first();

            $order->order_status = "canceled";
            $order->save();

            return response()->json(["message" => "Order canceled"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function showUser()
    {
        try {
            //code...
            $user = auth()->user();
            $response = [
                "id" => $user->id,
                "name" => $user->name,
                "profile_photo" => $user->profile_photo_path ?? asset('images/no_picture.jpg'),
                "phone" => $user->phone,
                "email" => $user->email
            ];

            // return response()->json($data);
            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            dd($th->getMessage());
        }
    }

    public function transaction(Request $request)
    {

        $user = auth()->user();

        $orders = Order::where('customer_id', $user->id)->latest();

        if ($request->has('status')) {

            $status = $request->get('status');

            if ($status === "ordered") {
                $orders->whereIn('order_status', ['waiting_payment']);
            } elseif ($status === "done") {
                $orders->where('order_status', 'done');
            }
        }

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $orders = $orders->limit($limit)->offset(($page - 1) * $limit);
        $data = $orders->get();
        $total = $orders->count();

        $data_arr = [];
        foreach ($data as $key => $value) {

            $service = null;
            if ($value->order_type == "regular") {
                foreach ($value->order_detail as $key => $d) {
                    $service[] = [
                        "id" => $d->id,
                        "name" => $d->name,
                        "media" => $d->image ?? '',
                        "price" => (int)$d->price
                    ];
                }
            }

            $review = ReviewService::where('order_number_id', $value->order_number)->first();

            $data_arr[] = [
                "id" => $value->order_number,
                "services" => $service,
                "custom_service" => ($value->order_type == "regular" ? "" : $value->order_detail[0]->name ?? ''),
                "destination" => json_decode($value->address)->description ?? '-',
                "reviewed" => isset($review) ? true : false,
                "created_at" => $value->created_at,
            ];
        }

        $response = [
            "data" => $data_arr,
            "page" => (int)$page,
            "size" => (int)$limit,
            "total" => (int)$total
        ];

        return response()->json($response);
    }

    public function transaction_on_going(Request $request)
    {
        $user = auth()->user();

        $orders = Order::where('customer_id', $user->id)
            ->whereIn('order_status', ['payment_success','waiting_order','accepted', 'processed', 'extend'])
            ->latest();

        $page = $request->has('page') ? $request->get('page') : 1;
        $limit = $request->has('size') ? $request->get('size') : 10;
        $orders = $orders->limit($limit)->offset(($page - 1) * $limit);
        $data = $orders->get();
        $total = $orders->count();

        $data_arr = [];
        foreach ($data as $key => $value) {

            $technician = null;
            if (isset($value->engineer_id)) {
                $technician = [
                    "technician_id" => (int)$value->engineer->id,
                    "name" => $value->engineer->name,
                    "media" => $value->engineer->profile_photo_path ?? '',
                    "rating" => (float)$value->engineer->rating??0
                ];
            }

            if(isset($technician)){
                $data_arr[] = [
                    "id" => $value->order_number,
                    "technician" => $technician,
                    "total_service" => (int)$value->order_type == "regular" ? $value->order_detail->count() : 0,
                    "is_custom" => $value->order_type == "regular" ? false : true,
                    "destination" => json_decode($value->address)->description ?? '-',
                    "created_at" => $value->created_at,
                ];
            }else{
                $data_arr[] = [
                    "id" => $value->order_number,
                    "total_service" => (int)$value->order_type == "regular" ? $value->order_detail->count() : 0,
                    "is_custom" => $value->order_type == "regular" ? false : true,
                    "destination" => json_decode($value->address)->description ?? '-',
                    "created_at" => $value->created_at,
                ];
            }
        }

        $response = [
            "data" => $data_arr,
            "page" => (int)$page,
            "size" => (int)$limit,
            "total" => (int)$total
        ];

        return response()->json($response);
    }

    public function payment_approval_store(Request $request, $order_id)
    {
        // dd(auth()->user()->id);
        try {
            //code...
            $bank = Bank::find($request->get('bank_id'));

            $order = Order::where('order_number', $order_id)->first();
            $deposit = Deposit::where('transfer_id', $order_id)->first();
            if (isset($order)) {
                $payment = Payment::create([
                    "customer_id" => auth()->user()->id,
                    "amount" => $order->total_payment - $order->convenience_fee ?? 0,
                    "paymentid" => "P" . uniqid(),
                    "convenience_fee" => $order->convenience_fee ?? 0,
                    "type" => $bank->name ?? "",
                    "status" => "check",
                    "orders" => $order_id,
                    "type_payment" => "order",
                    "data_id" => $order_id,
                    "account_holder" => $request->account_holder,
                    "account_number" => $request->account_number,
                    "bank_id" => $request->get('bank_id')
                ]);
            } elseif (isset($deposit)) {
                $payment = Payment::create([
                    "customer_id" => auth()->user()->id,
                    "amount" => $deposit->amount ?? 0,
                    "paymentid" => "P" . uniqid(),
                    "convenience_fee" => $deposit->unique_code ?? 0,
                    "type" => $bank->name ?? "",
                    "status" => "check",
                    "orders" => $order_id,
                    "type_payment" => "deposit",
                    "data_id" => $order_id,
                    "account_holder" => $request->account_holder,
                    "account_number" => $request->account_number,
                    "bank_id" => $request->get('bank_id')
                ]);
            }

            if ($request->hasFile('invoice_picture')) {

                $uploadFolder = 'users/photo/payment';
                $photo = $request->file('invoice_picture');
                $photo_path = $photo->store($uploadFolder, 'public');

                $payment->image = Storage::disk('public')->url($photo_path);
                $payment->save();
            }

            return response()->json(['message' => 'payment-approval store success']);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function withdraw(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
            'account_number' => 'required',
            'account_name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        $user = User::find(auth()->user()->id);

        $check = Withdraw::where('user_id',$user->id)
                            ->where('status','pending')
                            ->get();
        if($check->isNotEmpty()){
            return response()->json(["message" => "Mohon tunggu pengajuan sebelumnya terkonfirmasi"], 422);
        }

        try {
            //code...
            $amount = $request->amount;

            if ($amount > $user->balance) {
                return response()->json(["message" => "Tidak dapat di proses"],422);
            }

            DB::beginTransaction();
            Withdraw::create([
                "user_id" => auth()->user()->id,
                "amount" => $amount,
                "withdraw_id" => "W" . uniqid(),
                "account_number" => $request->account_number,
                "account_holder" => $request->account_name,
                "balance_before" => $user->balance
            ]);

            // $user->balance = $user->balance - $amount;
            // $user->save();

            DB::commit();

            return response()->json(['message' => 'withdraw successfully created']);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function deposit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $unique_code = mt_rand(100, 999);
            $amount = $request->get('amount');

            $deposit = Deposit::create([
                "customer_id" => auth()->user()->id,
                "transfer_id" => "TF" . uniqid(),
                "expired_date" => now()->addHour(),
                "amount" => $amount,
                "unique_code" => $unique_code,
                "total_amount" => $amount + $unique_code
            ]);

            $response = [
                "transfer_id" => $deposit->transfer_id,
                "expired_date" => $deposit->expired_date,
                "amount" => $deposit->amount,
                "unique_code" => $deposit->unique_code,
                "total_amount" => $deposit->total_amount
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function show_deposit($id)
    {
        try {
            //code...

            $deposit = Deposit::where('transfer_id',$id)->first();

            $response = [
                "transfer_id" => $deposit->transfer_id,
                "expired_date" => $deposit->expired_date,
                "amount" => (int)$deposit->amount,
                "unique_code" => (int)$deposit->unique_code,
                "total_amount" => (int)$deposit->total_amount
            ];

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function destroy_deposit($id)
    {

        if (is_null($id)) {
            return response()->json(["message" => "id is required"], 422);
        }

        try {
            //code...
            $deposit = Deposit::where('transfer_id', $id)->first();
            $deposit->delete();

            return response()->json(["message" => "Deposit canceled"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function get_custom_category(Request $request)
    {
        try {
            //code...
            $data = BaseService::whereHas('service_category', function ($query) {
                $query->where('name', 'like', '%custom%');
            });

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $service = $data->limit($limit)->offset(($page - 1) * $limit);
            $datas = $service->get();
            $total = $service->count();

            $data_arr = [];
            foreach ($datas as $key => $value) {
                # code...
                $data_arr[] = [
                    "id" => $value->id,
                    "name" => $value->name,
                    "item_name" => "",
                    "media" => $value->image
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

    public function notification(Request $request)
    {

        try {

            $user = auth()->user();
            $data = Notification::where('type', 'customer')
                ->where('user_id', $user->id)->latest();

            $page = $request->has('page') ? $request->get('page') : 1;
            $limit = $request->has('size') ? $request->get('size') : 10;
            $notif = $data->limit($limit)->offset(($page - 1) * $limit);
            $datas = $notif->get();
            $total = $notif->count();

            $datas = $datas->map(function ($data) {
                $avatar = "";
                if($data->action=="OPEN_ORDER_DETAIL"){
                    $avatar = $data->order->engineer->profile_photo_path??'';
                }
                return [
                    "id"        => $data->id,
                    "avatar"    => $avatar,
                    "image"     => $avatar,
                    "unread"    => ($data->read == false ? true : false),
                    "title"     => $data->title,
                    "subtitle"  => $data->subtitle,
                    "subtitle_color" => $data->subtitle_color == null ? "" : $data->subtitle_color,
                    "caption" => $data->caption??'',
                    "id_data" => $data->id_data_string ?? '',
                    "action"    => $data->action??'',
                    "created_at" => $data->created_at
                ];
            });

            $response['page'] = (int)$page;
            $response['size'] = (int)$limit;
            $response['total'] = (int)$total;
            $response['data'] = $datas;

            return response()->json($response);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function notification_read(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'notification_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $id = $request->get('notification_id');
            $notif = Notification::find($id);
            $notif->read = true;
            $notif->save();

            return response()->json(["message" => "notification read success"]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }

    public function store_review(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'rating' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            DB::beginTransaction();

            $rating = $request->get('rating');

            $review = ReviewService::create([
                "order_number_id" => $request->get('order_id'),
                "ratings" => $rating,
                "liked" => $request->get('likes') ?? [],
                "description" => $request->get('review_reason')
            ]);

            $order = Order::where('order_number', $request->get('order_id'))->first();

            $rating_engineer = $order->engineer->rating;
            $counter_engineer = $order->engineer->counter;

            $order->engineer->rating = ($rating_engineer+$rating)/($counter_engineer+1);
            $order->engineer->counter = $counter_engineer+1;
            $order->engineer->save();

            $title = auth()->user()->name . " telah memberikan rating";

            Notification::create([
                "title" => $title,
                "type" => "review",
                "user_id" => $order->engineer_id,
                "id_data" => $review->id,
                "id_data_string" => $order->order_number,
                "subtitle" => "Rating : ".$request->get('rating'),
                "action" => "OPEN_REVIEW_SCREEN"
            ]);

            $technician = User::find($order->engineer_id);
            $fcm_token[] = $technician->fcm_token;
            fcm()->to($fcm_token)
                ->priority('high')
                ->timeToLive(60)
                ->notification([
                    'title' => $title,
                    'body' => "Rating : " . $request->get('rating'),
                ])
                ->send();

            DB::commit();

            return response()->json(["message" => "Review submit success"]);
        } catch (\Throwable $th) {
            //throw $th;
            DB::rollBack();
            return response()->json(["message" => "Terjadi kesalahan " . $th->getMessage()], 422);
        }
    }
}
