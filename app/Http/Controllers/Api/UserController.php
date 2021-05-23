<?php

namespace App\Http\Controllers\Api;

use App\Models\User;
use App\Models\Engineer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class UserController extends Controller
{
    //

    public function index(){
        $data['success'] = true;
        $data['message'] = "Hai.";
        $data['data'] = "";
        return response()->json($data);
    }

    public function categoryservice(){
        return response()->json("hai ini categori service");   
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $user = User::Role('teknisi')->where('email', $credentials['email'])->first();
        
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

        // $user = User::Role('teknisi')->where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            // dd($user);
            // \Carbon\Carbon::setLocale('id');
            // $currentDateTime = Carbon::now();
            $newDateTime = Carbon::now()->addDays(30);
                
            // print_r($currentDateTime);
            // print_r($newDateTime);

            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

            $data['success'] = true;
            $data['message'] = "Login successfully";
            $data['data'] = $user;
            $data['token'] = $token;
            // $data['time1'] = $currentDateTime->format('Y-m-d H:i:s');
            $data['valid_until'] = $newDateTime->format('Y-m-d H:i:s');

            $data['token_type'] = "Bearer";
            
            return response()->json($data);
        }else{
            return response()->json(['message' => 'Opps... email atau kata sandi salah'], 422);
        }
    
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'phone' => 'required',
            'id_card_number' => 'required',
            'address' => 'required',
            'id_card' => 'required|mimes:img,png,jpeg,jpg|max:2048',
            'selfie_id_card' => 'required|mimes:img,png,jpeg,jpg|max:2048',
            'photo' => 'required|mimes:img,png,jpeg,jpg|max:2048',
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
                "address"           => $request->get('address'),
                "id_card_number"    => $request->get('id_card_number'),
                'userid'            => $userid,
                'password'          => Hash::make($request->get('password')),
            ]);

            $user->assignRole('teknisi');

            $engineer = Engineer::create([
                                "id_card_number" => $request->get('id_card_number'),
                                "name" => $request->get('name'),
                                'email'             => $request->get('email'),
                                "phone"             => $request->get('phone'),
                                "address"           => $request->get('address'),
                                "user_id"           => $user->id,
                            ]);

            $uploadFolder = 'users/card_id';
            $id_card_image = $request->file('id_card');
            $id_card_image_path = $id_card_image->store($uploadFolder, 'public');
            
            // 
            $uploadFolder = 'users/selfie_card_id';
            $id_card_selfie_image = $request->file('selfie_id_card');
            $id_card_selfie_path = $id_card_selfie_image->store($uploadFolder,'public');
            
            $uploadFolder = 'users/photo';
            $photo = $request->file('photo');
            $photo_path = $photo->store($uploadFolder,'public');
    
            $engineer->id_card_image = Storage::disk('public')->url($id_card_image_path);
            $engineer->id_card_selfie_image = Storage::disk('public')->url($id_card_selfie_path);
            $engineer->save();

            $user->profile_photo_path = Storage::disk('public')->url($photo_path);
            $user->save();

            DB::commit();

            // \Mail::to($request->get('email'))
            //         ->send(new \App\Mail\OtpMail($otp));

            $message = "Register berhasil";

            return response()->json(compact('message'),200);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=>$th->getMessage()],422);
        }
    }

    public function testing(){

        try {
            //code...
            $email = 'wa.pangestu16@gmail.com';
            $otp = rand(1000,9999);
            \Mail::to($email)
                ->send(new \App\Mail\OtpMail($otp));

            return response()->json(["success"=>true]);
        } catch (\Throwable $th) {
            return response()->json(["message"=>$th->getMessage()]);
            //throw $th;
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

        $user = User::where('email',$email)->first();

        if(is_null($user)){
            // $message = 'Email tidak ditemukan';
            return response()->json(["message"=>"Email Tidak ditemukan"], 422);
        }else{
            
            if($user->code_otp===$code_otp){
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->engineer->is_varified_email = true;
                $user->engineer->varified_email_at = date('Y-m-d H:i:s');
                $user->engineer->save();
                $message = 'konfirmasi kode otp berhasil';
                return response()->json(compact('message'));
            }else{
                $message = 'kode otp salah';
                return response()->json(["message"=>$message], 423);
            }
        }
    }

    public function forgot_password(Request $request)
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
            return response()->json("Terjadi kesalahan ".$th->getMessage());
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

        $user = User::where('email',$email)->first();

        if(is_null($user)){
            $message = 'Email tidak ditemukan';
            return response()->json(["message"=>$message], 422);
        }else{
            if($user->code_otp===$code_otp){
                $message = 'konfirmasi kode otp berhasil';
                $user->engineer->is_varified_email = true;
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->engineer->varified_email_at = date('Y-m-d H:i:s');
                $user->engineer->save();
                $kode=200;
            }else{
                $message = 'kode otp salah';
                $kode=423;
            }
            return response()->json(['message'=>$message],$kode);
        }
    }    

    public function change_password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()->all()[0]], 422);
        }

        $email = $request->get('email');
        $password = $request->get('password');

        try {
            //code...
            $user = User::where('email',$email)->first();
    
            if(is_null($user)){
                $message = 'Email tidak ditemukan';
                return response()->json(["message"=>$message], 422);
            }else{
                $user->password = Hash::make($password);
                $user->save();
                $message = "Password berhasil diubah";
                $kode = 200;
                return response()->json(['message'=>$message],$kode);
            }
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()], 422);
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
    }

    public function getAuthenticatedUser()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

    }

    public function store_fcm_token(Request $request)
    {
        $this->getAuthenticatedUser();
        
        $validator = Validator::make($request->all(), [
            'token' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()->all()[0]], 422);
        }
        
        try {
            //code...
            $user = User::find(auth()->user()->id);

            $user->fcm_token = $request->get('token');
            $user->save();

            return response()->json(["message" => "fcm token successfully updated"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi Kesalahan : ".$th->getMessage()], 422);
        }
        
    }

    public function delete_fcm_token(Request $request)
    {
        try {
            //code...
            $user = User::find(auth()->user()->id);

            $user->fcm_token = null;
            $user->save();

            return response()->json(["message" => "fcm token successfully deleted"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi Kesalahan : ".$th->getMessage()], 422);
        }

    }

    public function userEngineer()
    {

        try {
            //code...
  
            $user = auth()->user();

            $response = [
                "id" => $user->id,
                "id_card_number" => $user->engineer->id_card_number,
                "name" => $user->name,
                "phone" => $user->phone,
                "email" => $user->email,
                "address" => $user->address,
                "userid" => $user->userid,
                "email_verified_at" => $user->email_verified_at,
                "profile_photo" => $user->profile_photo_path,
                "is_active" => $user->is_active,
                "lat" => (float)$user->lat,
                "lng" => (float)$user->lng,
                "verified" => $user->verified,
                "last_login" => $user->last_login,
            ];
            
            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }

    }

    public function userCustomer(){

        try {
            //code...
            $user = auth()->user();

            $response = [
                "id" => $user->id,
                "name" => $user->name,
                "profile_photo" => $user->profil_photo_path?? asset('images/no_picture.jpg'),
                "phone" => $user->phone,
                "email" => $user->email
            ];

            // return response()->json($data);
            return response()->json($response);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message" => "Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function updateEngineer(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.auth()->user()->id,
            'phone' => 'required|unique:users,phone,'.auth()->user()->id,
            'id_card_number' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $user = User::find(auth()->user()->id);

            $user->id_card_number = $request->id_card_number;
            $user->is_active = $request->status==="active"?1:0;
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

    public function EngineerBalance()
    {

        try {
            //code...
            $user = auth()->user();

            if($user->verified === 0){
                return response()->json(["message"=>"Akun sedang diverifikasi"],423);
            }
    
            $balance = (int)$user->balance;
    
            return response()->json(compact('balance'));
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function change_password_user(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...
            $user = User::find(auth()->user()->id);

            $check = Hash::check($request->old_password,$user->password);
            if(!($check)){
                return response()->json(["message"=>"Password lama salah"],422);                
            }

            $user->password = Hash::make($request->new_password);
            $user->save();

            return response()->json(["message"=>"Password berhasil diubah"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

    public function update_user_profile(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'mimes:img,png,jpeg,jpg|max:2048',
        ]);

        if($validator->fails()){
            return response()->json(["message" => $validator->errors()->all()[0]], 422);
        }

        try {
            //code...

            $user = User::find(auth()->user()->id);

            if($request->hasFile('image')){

                $uploadFolder = 'users/photo';
                $photo = $request->file('image');
                $photo_path = $photo->store($uploadFolder,'public');
            
                $user->profile_photo_path = Storage::disk('public')->url($photo_path);
                $user->save();                
            }

            return response()->json(["message" => "Photo berhasil di ubah"]);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json(["message"=>"Terjadi kesalahan ".$th->getMessage()],422);
        }
    }

}
