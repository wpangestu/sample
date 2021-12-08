<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Exception;
use Illuminate\Support\Facades\Validator;

class UserService {

    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;        
    }

    public function getUserFromToken()
    {
        return $this->userRepository->getUserFromToken();
    }

    public function updateBasicInfoUser($data)
    {
        $user = $this->userRepository->getUserFromToken();

        $validator = Validator::make($data,[
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'required|unique:users,phone,' . $user->id,
        ]);

        if($validator->fails()){
            throw new Exception($validator->errors()->first(),422);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];
        $user->save();

        return $user;

    }

    public function getTopUserAddress()
    {
        return $this->userRepository->getUserTopAddress(4);
    }
}