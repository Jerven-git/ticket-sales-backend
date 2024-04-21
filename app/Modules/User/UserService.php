<?php

namespace App\Modules\User;

use App\Models\User;
use App\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Create new User
     * 
     * @param array $userData
     * 
     * $userData Contains the user data
     * 
     * @return User
     */
    public function create(array $userData): User|array
    {
        return $this->userRepository->create($userData);
    }
}