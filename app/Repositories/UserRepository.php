<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserAddress;

class UserRepository
{

    private $user;

    public function __construct(User $user){
        $this->user = $user;
    }

    public function getByEmail($email): ?User
    {
        return $this->user->where('email',$email)->first();
    }

    public function getByUserid($userid): ?User
    {
        return $this->user->where('userid',$userid)->first();
    }

    public function save($data): User
    {
        return $this->user->create($data);
    }

    public function getUserFromToken():User
    {
        return auth()->user();
    }

    public function getUserTopAddress(int $limit)
    {
        $user = $this->getUserFromToken();
        
        $topAddressUser = UserAddress::where('user_id',$user->id)->limit($limit);

        return $topAddressUser;
    }


}