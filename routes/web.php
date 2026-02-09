<?php

use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;

// 當使用者進入首頁時，由 MapController 的 index 函式處理
Route::get('/', [MapController::class, 'index']);
Route::post('/places', [App\Http\Controllers\MapController::class, 'store']);
Route::delete('/places/{id}', [App\Http\Controllers\MapController::class, 'destroy']);
Route::get('/ai-plan', [App\Http\Controllers\MapController::class, 'aiPlan']);