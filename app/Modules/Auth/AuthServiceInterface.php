<?php

namespace App\Modules\Auth;

interface AuthServiceInterface
{
    public function forgotPassword(string $email);
    
    public function resetPassword(string $email, string $token, string $password);
}