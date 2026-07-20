<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Web authentication routes (session-based)
Route::middleware('web')->group(function () {

    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    
    // SPA catch-all route must be in web middleware for CSRF to work
    Route::get('/{any}', function () {
        return view('index');
    })->where('any', '.*');
});