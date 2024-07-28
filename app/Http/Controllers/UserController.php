<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Modules\User\UserServiceInterface;
use App\Models\User;

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
        $userData = $request->only(['first_name', 'last_name', 'email', 'password']);
        $user = $this->userService->create($userData);
    
        if (isset($user['errors'])) {
            return response()->json(['errors' => $user['errors']], 422);
        }
    
        return response()->json(['message' => 'User created successfully'], 201);
    }    
    /**
     * Update user.
     * 
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        //Note: this function only works if the user is authenticated
        // $user = $request->user();
        
        $userData = $request->only(['first_name', 'last_name', 'email', 'password']);

        $isSuccess = $this->userService->update($user, $userData);

        if (isset($isSuccess['errors'])) {
            return response()->json(['errors' => $isSuccess['errors']], 422);
        }

        return response()->json(['message' => 'User updated successfully'], 201);
    }
}