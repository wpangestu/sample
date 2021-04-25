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
}
