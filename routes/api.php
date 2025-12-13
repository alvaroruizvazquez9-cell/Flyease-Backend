<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\FlightController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\Admin\AdminFlightController;

// Públicas
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Vuelos (Público)
Route::get('/flights', [FlightController::class, 'index']);
Route::get('/flights/{id}', [FlightController::class, 'show']);


// Autenticado
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);


    // Reservas
    Route::apiResource('bookings', BookingController::class)->only(['index', 'store', 'destroy']);
    Route::post('/bookings/confirm', [BookingController::class, 'confirmPayment']);
    Route::patch('/bookings/{id}/cancel', [BookingController::class, 'cancel']);

    // Admin
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::get('/stats', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'stats']);
        Route::apiResource('flights', AdminFlightController::class);
        Route::apiResource('bookings', \App\Http\Controllers\Admin\AdminBookingController::class)->only(['index', 'show', 'destroy']);
        Route::apiResource('users', \App\Http\Controllers\Admin\AdminUserController::class);
    });
});