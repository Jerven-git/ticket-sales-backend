<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;
use App\Modules\Auth\AuthServiceInterface;

class AuthenticationController extends Controller
{
    protected AuthServiceInterface $authService;

    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }
    /**
     * Handle an authentication attempt.
     *
     */
    public function authenticate(Request $request)
    {
        try {
            // grab credentials from the request
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'min:8'], // Minimum length of 8 characters
            ]);
        } catch (ValidationException $e) {
            return response()->json(['error' => 'invalid_credentials'], 401);
        }
        
        $exposeAttr = [
            'id',
            'first_name',
            'last_name',
            'email',
        ];

        //This code assumes that the user is not logged in, sets user to null and sets session
        $response = [
            'isLoggedIn' => false,
            'user' => null,
            'session' => (object)[
                'lifetime' => intval(env('SESSION_LIFETIME', 120))
            ]
        ];

        //
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $response['isLoggedIn'] = true;
            $response['user'] = $request->user()->only($exposeAttr);

            //Token for POSTMAN

            // $token = $request->user()->createToken('aussie', ['*'], now()->addDays(7));
            // $response['token'] = $token->plainTextToken;
            
            $message = 'User successfully logged in';
        }
        return response($message);
    }

    /**
     * Handle password reset requests
     *
     */
    public function forgotPassword(Request $request)
    {
        $email = $request->input('email');

        $this->authService->forgotPassword($email);

        return response()->json(['message' => 'Reset request processed, If we find the email in our database, you will receive the link via email to reset your password'], 200);
    }

    /**
     * Reset password request due to forgotten password
     * 
     */
    public function resetPassword(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $passwordConfirmation = $request->input('password');
        $token = $request->input('token');

        $isSuccess = $this->authService->resetPassword($email, $password, $passwordConfirmation, $token);

        if ($isSuccess) {
            return response()->json(['message' => 'Password reset successfully'], 200);
        } else {
            return response()->json(['message' => 'Password reset failed'], 400);
        }
    }

    /**
     * Invalidate the user's session
     * 
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json(['message' => 'User logged out successfully'], 200);
    }
}
