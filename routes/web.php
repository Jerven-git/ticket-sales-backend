<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthenticationController;
use Laravel\Sanctum\Http\Controllers\CsrfCookieController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('web')->get('/api/sanctum/csrf-cookie', [CsrfCookieController::class, 'show']);

Route::prefix('api')->group(function () {
    Route::prefix('v1')->group(function () {
        Route::prefix('auth')->group(function () {
            // Protected routes that require authentication
            Route::middleware('auth:sanctum')->group(function () {
                Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
                Route::get('check', [AuthenticationController::class, 'checkAuth']);
            });

            // Routes that do not require authentication
            Route::post('login', [AuthenticationController::class, 'authenticate'])->name('login');
            Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword'])->name('forgotPassword');
            Route::post('reset-password', [AuthenticationController::class, 'resetPassword'])->name('resetPassword');
        });
    });
});