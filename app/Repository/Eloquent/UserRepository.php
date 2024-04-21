<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use App\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $request;

    public function __construct(User $user, Request $request)
    {
        parent::__construct($user);
        $this->request = $request;
    }

    public function create(array $userData): User|array
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];

        // Validate data
        $validator = Validator::make($userData, $rules);

        // Check if validation fails
        if ($validator->fails()) {
            // Handle validation failure, return error messages
            return ['errors' => $validator->errors()->all()];
        }

        // Validation passed, create user
        $user = User::create($userData);

        return $user;
    }
}