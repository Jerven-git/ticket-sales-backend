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
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required', 'min:6'],
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
    
        // Initialize response
        $response = [
            'isLoggedIn' => false,
            'user' => null,
            'session' => (object)[
                'lifetime' => intval(env('SESSION_LIFETIME', 120))
            ]
        ];
    
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $response['isLoggedIn'] = true;
            $response['user'] = $request->user()->only($exposeAttr);
    
            // Generate token
            $token = $request->user()->createToken('authToken', ['*'], now()->addDays(7))->plainTextToken;
    
            // Set token in HTTP-only cookie
            $cookie = cookie('authToken', $token, 60 * 24 * 7, null, null, true, true);
    
            // Return response with cookie
            return response()->json(['message' => 'User successfully logged in', 'data' => $response])->cookie($cookie);
        } else {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }
    }

    public function checkAuth(Request $request)
    {
        if (Auth::check()) {
            $user = Auth::user();
            return response()->json([
                'isLoggedIn' => true,
                'user' => [
                    'id' => $user->id,
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                ],
            ]);
        } else {
            return response()->json([
                'isLoggedIn' => false,
                'user' => null,
            ]);
        }
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
        $token = $request->input('token');

        $isSuccess = $this->authService->resetPassword($email, $token, $password);

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
