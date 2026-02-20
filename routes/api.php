<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\OrganizerController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::prefix('v1')->group(function () {
    Route::prefix('sign-up')->group(function () {
        Route::post('/', [UserController::class, 'store']);
        Route::patch('/update/{id}', [UserController::class, 'update']);
    });

    // Public event routes
    Route::prefix('events')->group(function () {
        Route::get('/', [EventController::class, 'index']);
        Route::get('/search', [EventController::class, 'search']);
        Route::get('/{id}', [EventController::class, 'show']);
        Route::get('/{eventId}/tickets', [TicketController::class, 'index']);
        Route::get('/{eventId}/tickets/{ticketId}', [TicketController::class, 'show']);
    });

    // Public organizer routes
    Route::prefix('organizers')->group(function () {
        Route::get('/', [OrganizerController::class, 'index']);
        Route::get('/{id}', [OrganizerController::class, 'show']);
    });

    // Stripe webhook (no auth, verified by Stripe signature)
    Route::post('/webhooks/stripe', [PaymentController::class, 'handleWebhook']);

    // Protected routes
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });

        Route::get('/my-events', [EventController::class, 'myEvents']);

        // Event management (authenticated)
        Route::prefix('events')->group(function () {
            Route::post('/', [EventController::class, 'store']);
            Route::put('/{id}', [EventController::class, 'update']);
            Route::delete('/{id}', [EventController::class, 'destroy']);

            // Ticket management (authenticated)
            Route::post('/{eventId}/tickets', [TicketController::class, 'store']);
            Route::put('/{eventId}/tickets/{ticketId}', [TicketController::class, 'update']);
            Route::delete('/{eventId}/tickets/{ticketId}', [TicketController::class, 'destroy']);
        });

        // Organizer management (authenticated)
        Route::prefix('organizers')->group(function () {
            Route::post('/', [OrganizerController::class, 'store']);
            Route::put('/{id}', [OrganizerController::class, 'update']);
            Route::delete('/{id}', [OrganizerController::class, 'destroy']);
        });

        // Order management (authenticated)
        Route::prefix('orders')->group(function () {
            Route::post('/', [OrderController::class, 'store']);
            Route::get('/', [OrderController::class, 'index']);
            Route::get('/{orderNumber}', [OrderController::class, 'show']);
        });

        // Payment (authenticated)
        Route::post('/payments/create-intent', [PaymentController::class, 'createPaymentIntent']);
    });
});
