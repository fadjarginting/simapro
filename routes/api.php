<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\Api\WorkApiController;

// Plant routes with prefix
Route::prefix('plants')->group(function () {
    Route::get('/', [PlantController::class, 'index']);
    Route::get('/{id}', [PlantController::class, 'show']);
});

// Work routes with prefix
Route::prefix('works')->group(function () {
    Route::post('/{workId}/assign-lead-engineer', [WorkApiController::class, 'assignLeadEngineer']);
}); 