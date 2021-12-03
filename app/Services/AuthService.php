<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Validator;

class AuthService
{

    protected $userRepository;

    public function __construct(UserRepository $userRepository){
        $this->userRepository = $userRepository;
    }

    public function login($data)
    {
        $validator = Validator::make($data,[
            "email" => 'required',
            "password" => 'required'
        ]);

        if($validator->fails()){
            throw new Exception($validator->errors()->first());
        }

        $user = $this->userRepository->getByEmail($data['email']);

        if($user==null){
            throw new Exception("Email or Password is wrong");
        }

        if(password_verify($data['password'],$user->password)){
            return $user;
        }else{
            throw new Exception("Email or Password is wrong");
        }
    }
}