<?php

namespace App\Modules\User;

use App\Models\User;

interface UserServiceInterface
{
    /**
     * Create new User
     * 
     * @param array $userData, Credentials of the user
     * 
     * @return User
     */
    public function create(array $userData): User|array;
    
    /**
     * Update User
     * 
     * @param User $user, The user who initiated the update
     * @param array $userData, Credentials of the user
     * 
     * @param array $userData
     */
    public function update(User $user, array $userData): User|array;
}