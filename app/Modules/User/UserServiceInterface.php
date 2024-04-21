<?php

namespace App\Modules\User;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Create new User
     * 
     * @param array $userData
     * 
     * $userData Contains the user data
     * 
     * @return User
     */
    public function create(array $userData): User|array;
}