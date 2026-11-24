<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CaravanaController;
use App\Http\Controllers\Api\ParadaController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// Públicas
Route::get('/caravanas', [CaravanaController::class, 'index']);
Route::get('/caravanas/{caravana}', [CaravanaController::class, 'show']);
Route::get('/caravanas/{caravana}/paradas', [ParadaController::class, 'index']);
Route::patch('/paradas/{parada}/estado', [ParadaController::class, 'updateEstado']);

// Solo JEFE_CARAVANA
Route::middleware(['auth:sanctum', 'rol:JEFE_CARAVANA'])->group(function () {
    Route::post('/caravanas', [CaravanaController::class, 'store']);
    Route::put('/caravanas/{caravana}', [CaravanaController::class, 'update']);
    Route::post('/caravanas/{caravana}/paradas', [ParadaController::class, 'store']);
    Route::delete('/paradas/{parada}', [ParadaController::class, 'destroy']);
});