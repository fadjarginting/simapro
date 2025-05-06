<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PlantController;
// Plant routes
Route::get('/plants', [PlantController::class, 'index']);
Route::get('/plants/{id}', [PlantController::class, 'show']);
