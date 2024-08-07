<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthenticationController;

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

Route::prefix('api')->group(function () {

    Route::prefix('v1')->group(function () {
        Route::prefix('auth')->group(function () {
            Route::post('login', [AuthenticationController::class, 'authenticate'])->name('login');
            Route::post('logout', [AuthenticationController::class, 'logout'])->name('logout');
            Route::post('forgot-password', [AuthenticationController::class, 'forgotPassword'])->name('forgotPassword');
            Route::post('reset-password', [AuthenticationController::class,'resetPassword'])->name('resetPassword');
            Route::get('check', [AuthenticationController::class, 'checkAuth']);
        });
    });

    Route::get('/csrf-token', function () {
        return response()->json(['token' => csrf_token()]);
    });
});