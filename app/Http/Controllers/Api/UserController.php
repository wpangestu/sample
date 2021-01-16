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

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid_credentials'], 400);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $user = User::where('email', $credentials['email'])->first();

        if ($user && Hash::check($credentials['password'], $user->password)) {
            $data['success'] = true;
            $data['message'] = "Login successfully";
            $data['data'] = $user;
            $data['token'] = $token;
        }
        

        return response()->json($data);
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
            return response()->json(["message" => $validator->errors()], 400);
        }

        $otp = mt_rand(1000,9999);

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
                'userid'            => Str::random(6),
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

            \Mail::to($request->get('email'))
                    ->send(new \App\Mail\OtpMail($otp));

            $message = "Register berhasil, silahkan cek email anda untuk memasukan kode otp";

            return response()->json(compact('message'),200);

        } catch (\Throwable $th) {
            //throw $th;
            DB::rollback();
            return response()->json(["message"=>$th->getMessage()],422);
        }
    }

    public function confirmation_otp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'code_otp' => 'required'
        ]);

        if($validator->fails()){
            return response()->json(["message"=>$validator->errors()], 400);
        }

        $email = $request->get('email');
        $code_otp = $request->get('code_otp');

        $user = User::where('email',$email)->first();

        if(is_null($user)){
            $message = 'Email tidak ditemukan';
        }else{
            
            if($user->code_otp===$code_otp){
                $message = 'konfirmasi kode otp berhasil';
                $user->engineer->is_varified_email = true;
                $user->engineer->varified_email_at = date('Y-m-d H:i:s');
                $user->engineer->save();
            }else{
                $message = 'kode otp salah';
            }
        }
        return response()->json(compact('message'));
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

        return response()->json(compact('user'));
    }

}
