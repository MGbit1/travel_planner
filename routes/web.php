<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController; // 💡 新增：引入 MapController
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 🤖 💡 新增：給 AI 生成行程專用的 API 路線
Route::post('/ai-generate', [MapController::class, 'generateAI']);

// 💾 💡 新增：給存檔功能專用的 API 路線
Route::post('/trips', [MapController::class, 'store']);

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';