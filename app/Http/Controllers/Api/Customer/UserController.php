<?php

namespace App\Http\Controllers\Api\Customer;

use App\Models\User;
use App\Mail\OtpMail;
use JWTAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
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
            return response()->json(["message"=>"Terjadi kesalahan ".$message], 422);
        }

    }    
}
