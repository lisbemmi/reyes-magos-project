<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaravanaController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Públicas
Route::get('/caravanas', [CaravanaController::class, 'index']);
Route::get('/caravanas/{caravana}', [CaravanaController::class, 'show']);

// Solo JEFE_CARAVANA
Route::middleware(['auth:sanctum', 'rol:JEFE_CARAVANA'])->group(function () {
    Route::post('/caravanas', [CaravanaController::class, 'store']);
    Route::put('/caravanas/{caravana}', [CaravanaController::class, 'update']);
});