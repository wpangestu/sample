<?php

namespace App\Repositories;

use App\Models\User;

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


}