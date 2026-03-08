<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController; 
use App\Http\Controllers\TripController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// 🤖 💡 AI 生成行程專用的 API 路線 (不用登入也能用)
Route::post('/ai-generate', [MapController::class, 'generateAI']);

// ⚠️ 注意：這裡原本舊的 /trips 路由已經被助教移除了！

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// 👇 必須「登入後」才能使用的功能群組
Route::middleware('auth')->group(function () {
    
    // 💾 💡 正確的存檔 API 路線：指向 TripController，並且被保護在 auth 群組內
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');

    // 會員中心預設路由
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';