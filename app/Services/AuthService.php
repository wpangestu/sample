<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function login($data) : User
    {
        $validator = Validator::make($data,[
            "email" => 'required|string|email',
            "password" => 'required',
            'device_id' => 'required',
        ]);

        if($validator->fails()){
            throw new Exception($validator->errors()->first(),422);
        }

        $user = $this->userRepository->getByEmail($data['email']);

        if($user==null){
            throw new Exception("Email belum terdaftar",423);
        } 

        if(password_verify($data['password'],$user->password)){
            if($user->device_id == null){
                $user->device_id = $data->device_id;
            }
            $user->last_login = date('Y-m-d H:i:s');
            return $user;
        }else{
            throw new Exception("Opps... email atau kata sandi salah",422);
        }
    }

    public function loginByGoogleId($data) : User
    {
        $validator = Validator::make($data,[
            "email" => 'required|string|email',
            'id_google' => 'required',
            'device_id' => 'required'
        ]);

        if($validator->fails()){
            throw new Exception($validator->errors()->first(),422);
        }

        $user = $this->userRepository->getByEmail($data['email']);

        if($user==null){
            throw new Exception("Email belum terdaftar",424);
        }

        if($user->id_google == $data['id_google']){

            if($user->device_id == null){
                $user->device_id = $data->device_id;
            }
            $user->last_login = date('Y-m-d H:i:s');
            $user->save();

            return $user;
        }else{
            throw new Exception("Akun Tidak ditemukan",425);
        }
    }

    public function generateToken(User $user)
    {
        $token = JWTAuth::fromUser($user);  
        $payload = JWTAuth::setToken($token)->getPayload();
        $expired_at = date('Y-m-d H:i:s', $payload->get('exp'));

        return [
            "token" => $token,
            "expired_at" => $expired_at,
            "token_type" => "Bearer"
        ];
    }


}