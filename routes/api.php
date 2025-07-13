<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EatController;
use Illuminate\Support\Facades\Request;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\Api\WorkApiController;

// Plant routes with prefix
Route::prefix('plants')->group(function () {
    Route::get('/', [PlantController::class, 'index']);
    Route::get('/{id}', [PlantController::class, 'show']);
});

// // Work routes with prefix
// Route::prefix('works')->group(function () {
//     Route::post('/{workId}/assign-lead-engineer', [WorkApiController::class, 'assignLeadEngineer']);
// }); 


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// EAT API Routes
Route::middleware('auth')->group(function () {
    // Get EAT data for a specific work
    Route::get('/works/{work}/eat', [EatController::class, 'getEATByWork']);
    
    // Get all disciplines
    Route::get('/disciplines', [EatController::class, 'getDisciplines']);
    
    // Get all users
    Route::get('/users', [EatController::class, 'getUsers']);
    
    // Existing EAT API routes
    Route::get('/eats', [EatController::class, 'apiIndex']);
    Route::get('/eats/{eat}', [EatController::class, 'apiShow']);
});