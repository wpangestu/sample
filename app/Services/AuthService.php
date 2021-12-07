<?php

namespace App\Services;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

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
            "token"      => $token,
            "expired_at" => $expired_at,
            "token_type" => "Bearer"
        ];
    }

    public function registerCustomer($data) : User
    {
        $validator = Validator::make($data, [
            'name'      => 'required|string|max:255',
            'phone'     => 'required',
            'email'     => 'required|string|email|max:255|unique:users',
            'password'  => 'required|string|min:6',
            'id_google' => 'nullable'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(),422);
        }
        
        try {

            DB::beginTransaction();

            $genereateUniqeUserid = $this->getUniqueUserid();

            $dataUser = [
                'code_otp'          => mt_rand(1000, 9999),
                'name'              => $data['name'],
                'email'             => $data['email'],
                "phone"             => $data['phone'],
                'userid'            => $genereateUniqeUserid,
                'password'          => Hash::make($data['password']),
                'id_google'         => $data['id_google'] ?? null
            ];

            $userCustomer = $this->userRepository->save($dataUser);

            $userCustomer->last_login = date('Y-m-d H:i:s');
            $userCustomer->save();
            $userCustomer->assignRole('user');
            
            DB::commit();
            
            return $userCustomer;

        } catch (Exception $e) {
            //throw $th;
            DB::rollback();
            throw new Exception($e->getMessage(),422);
        }

    }

    public function getUniqueUserid(): int
    {
        $check = true;

        while ($check) {
            # code...
            $userid = mt_rand(100000, 999999);
            $checkUser = $this->userRepository->getByUserid($userid);
            if (empty($checkUser)) {
                $check = false;
            }
        }

        return $userid;
    }

    public function checkValidDeviceId($data): bool
    {
        $validator = Validator::make($data,[
            'device_id' => 'required'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(),422);
        }

        if($data['device_id'] == auth()->user()->device_id){
            return true;
        }
        return false;
    }

    public function generateNewOtpUser($data) : int
    {
        $validator = Validator::make($data,[
            'email' => 'required|email'
        ]);
        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(),422);
        }

        $user = $this->userRepository->getByEmail($data['email']);

        if(empty($user)){
            throw new Exception("Email not found",422);
        }

        $newOtp = (int)mt_rand(1000, 9999);
        $user->code_otp = $newOtp;
        $user->save();

        return $newOtp;
    }

    public function confirmationOtp($data):bool
    {
        $validator = Validator::make($data,[
            'email' => 'required|email',
            'code_otp' => 'required'
        ]);

        if ($validator->fails()) {
            throw new Exception($validator->errors()->first(),422);
        }

        $user = $this->userRepository->getByEmail($data['email']);

        if(empty($user)){
            throw new Exception("Email not found", 422);
        }

        if($user->code_otp == $data['code_otp']){
            $user->email_verified_at = date('Y-m-d H:i:s');
            $user->save();
            return true;
        }
        return false;
    }
}