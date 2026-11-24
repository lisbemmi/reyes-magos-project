<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Exceptions\TransicionInvalidaException;

Route::get('/prueba/409', fn () => throw new TransicionInvalidaException('DETENIDA', 'EN_BELEN'));
Route::post('/prueba/400', fn (Request $r) => $r->validate(['origen' => 'required|string']));
Route::middleware('auth:sanctum')->get('/prueba/protegida', fn () => ['ok' => true]);