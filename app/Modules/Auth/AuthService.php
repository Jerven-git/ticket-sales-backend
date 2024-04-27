<?php

namespace App\Modules\Auth;

use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


class AuthService implements AuthServiceInterface
{
    public function forgotPassword(string $email)
    {
        $status = Password::sendResetLink(
            [
                'email' => $email,
            ]
        );
        if ($status === Password::RESET_THROTTLED) {
            return response()->json([
               'message' => 'Too many password reset attempts. Please try again in a minute.',
            ], 429);
        }
        
        return true;
    }

    public function resetPassword(string $email, string $token, string $password)
    {
        $credentials = [
            'token' => $token,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $password,
        ];
        $status = Password::reset($credentials, function ($user, $password) {
            $user
                ->forceFill([
                    'password' => Hash::make($password)
                ])
                ->setRememberToken(Str::random(60));

            $user->save();

            event(new PasswordReset($user));
        });
        return $status === Password::PASSWORD_RESET;
    }
}