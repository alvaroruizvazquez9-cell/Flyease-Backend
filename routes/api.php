<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\User\FlightController;
use App\Http\Controllers\User\BookingController;
use App\Http\Controllers\Admin\AdminFlightController;

// Público
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Autenticado
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    // Vuelos
    Route::get('/flights', [FlightController::class, 'index']);
    Route::get('/flights/{id}', [FlightController::class, 'show']);

    // Reservas
    Route::apiResource('bookings', BookingController::class)->only(['index', 'store']);
    Route::post('/bookings/confirm', [BookingController::class, 'confirmPayment']);
    Route::delete('/bookings/{id}', [BookingController::class, 'cancel']);

    // Admin
    Route::middleware('admin')->prefix('admin')->group(function () {
        Route::apiResource('flights', AdminFlightController::class);
    });
});