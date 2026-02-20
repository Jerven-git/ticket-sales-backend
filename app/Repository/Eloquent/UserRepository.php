<?php

namespace App\Repository\Eloquent;

use App\Repository\Eloquent\Base\BaseRepository;
use App\Repository\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    protected $user;

    public function __construct(User $user)
    {
        parent::__construct($user);
        $this->user = $user;
    }

    public function create(array $userData): User|array
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ];

        $validator = Validator::make($userData, $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()->all()];
        }

        $userData['password'] = Hash::make($userData['password']);

        $user = User::create($userData);

        return $user;
    }

    public function update(int $id, array $userData): User|array
    {
        $rules = [
            'first_name' =>'required',
            'last_name' =>'required',
            'email' =>'required|email',
            'password' => 'required|min:6'
        ];
        
        $validator = Validator::make($userData, $rules);

        if ($validator->fails()) {
            return ['errors' => $validator->errors()->all()];
        }

        $user = $this->user->find($id);

        if (isset($userData['password'])) {
            $userData['password'] = Hash::make($userData['password']);
        }

        $isSuccess = DB::transaction(function () use ($user, $userData) {
            $user->update($userData);
            return true;
        });

        return ['success' => $isSuccess];
    }
}