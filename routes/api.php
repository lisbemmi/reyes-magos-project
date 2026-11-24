<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

// TEMPORAL: para probar 401/403.
Route::middleware(['auth:sanctum', 'rol:JEFE_CARAVANA'])
    ->get('/prueba/solo-jefe', fn () => ['ok' => true]);