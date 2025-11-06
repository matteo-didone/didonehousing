<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\ListingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Protected routes (require authentication)
Route::middleware('auth:sanctum')->group(function () {
    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword']);
    Route::patch('/profile/role-profile', [ProfileController::class, 'updateRoleProfile']);

    // Properties
    Route::apiResource('properties', PropertyController::class);
    Route::post('/properties/{property}/submit', [PropertyController::class, 'submitForReview']);
    Route::post('/properties/{property}/approve', [PropertyController::class, 'approve']);
    Route::post('/properties/{property}/reject', [PropertyController::class, 'reject']);

    // Listings
    Route::apiResource('listings', ListingController::class);
    Route::post('/listings/{listing}/submit', [ListingController::class, 'submit']);
    Route::post('/listings/{listing}/start-review', [ListingController::class, 'startReview']);
    Route::post('/listings/{listing}/approve', [ListingController::class, 'approve']);
    Route::post('/listings/{listing}/reject', [ListingController::class, 'reject']);
    Route::post('/listings/{listing}/publish', [ListingController::class, 'publish']);
    Route::post('/listings/{listing}/unpublish', [ListingController::class, 'unpublish']);
});

// Health check
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
    ]);
});
