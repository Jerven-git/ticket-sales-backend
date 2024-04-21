<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\User\UserServiceInterface;

class UserController extends Controller
{
    /**
     * User Module
     * @var UserServiceInterface $userService
     */
    protected UserServiceInterface $userService;
    /**
     * User Controller Constructor
     * 
     * @param UserServiceInterface $userService
     * 
     * @return void
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }
    /**
     * Store a newly created resource in storage.
     * 
     * sign-up
     */
    public function store(Request $request)
    {
        // Use only() method to specify multiple keys
        $userData = $request->only(['first_name', 'last_name', 'email', 'password']);
    
        $response = $this->userService->create($userData);
    
        if (isset($response['errors'])) {
            return response()->json(['errors' => $response['errors']], 422);
        }
    
        return response()->json(['message' => 'User created successfully'], 201);
    }    
}