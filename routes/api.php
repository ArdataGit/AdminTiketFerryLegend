<?php

use App\Http\Controllers\VehicleTypeController;
use Illuminate\Http\Request;
use App\Http\Controllers\userController;
use Illuminate\Support\Facades\Route;

Route::get('/vehicle-types', [VehicleTypeController::class, 'getVehicleTypes'])->name('vehicle-types.get');



// Auth dasar
Route::post('/login', [userController::class, 'login']);
Route::post('/register', [userController::class, 'register']);

// Google OAuth (WEB redirect)
Route::get('/auth/google', [userController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [userController::class, 'handleGoogleCallback']);

// Google Login via API (Mobile / SPA)
Route::post('/login/google', [userController::class, 'loginWithGoogle']);

Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/logout', [userController::class, 'logout']);

    // Profile
    Route::get('/profile', [userController::class, 'profile']);
    Route::put('/profile', [userController::class, 'updateAccount']);

    // User management (admin panel via API)
    Route::get('/users', [userController::class, 'data']);
    Route::post('/users', [userController::class, 'store']);
    Route::put('/users/{id}', [userController::class, 'update']);
    Route::delete('/users/{id}', [userController::class, 'destroy']);
});