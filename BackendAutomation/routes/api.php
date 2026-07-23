<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\VideoController;
use App\Http\Controllers\Api\AutomationController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HistoryController;

// Public Auth Routes (Supports both /register and /auth/register, /login and /auth/login)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/auth/login', [AuthController::class, 'login']);

Route::post('/refresh', [AuthController::class, 'refresh']);
Route::post('/auth/refresh', [AuthController::class, 'refresh']);

// Protected Routes (Require Sanctum Access Token)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return response()->json([
            'success' => true,
            'user' => $request->user()
        ]);
    });

    // Videos API Routes
    Route::get('/videos', [VideoController::class, 'index']);
    Route::post('/videos', [VideoController::class, 'store']);
    Route::post('/videos/batch', [VideoController::class, 'storeBatch']);

    // Automation API Routes
    Route::post('/automation/start', [AutomationController::class, 'start']);

    // History API Routes (Supports both /histories and /history)
    Route::get('/histories', [HistoryController::class, 'index']);
    Route::get('/history', [HistoryController::class, 'index']);
    Route::get('/histories/{id}', [HistoryController::class, 'show']);
    Route::get('/history/{id}', [HistoryController::class, 'show']);
});

// Unprotected Webhook Callback (Called by Python Service)
Route::post('/automation/callback', [AutomationController::class, 'callback']);
