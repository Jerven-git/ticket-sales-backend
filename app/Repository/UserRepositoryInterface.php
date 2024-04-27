<?php

namespace App\Repository;

use App\Models\User;

interface UserRepositoryInterface
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

    /**
     * Update User
     * 
     * @param int $id
     * @param array $userData
     */
    public function update(int $id, array $userData): User|array;
}