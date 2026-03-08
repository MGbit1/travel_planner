<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController; 
use App\Http\Controllers\TripController; 
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/ai-generate', [MapController::class, 'generateAI']);

// 💡 1. 修改 Dashboard 路由：把使用者的行程撈出來傳給視圖
Route::get('/dashboard', function () {
    // 撈取目前登入者的所有行程，依照時間最新排在最前面
    $trips = \App\Models\Trip::where('user_id', auth()->id())->latest()->get();
    return view('dashboard', compact('trips'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 必須「登入後」才能使用的功能群組
Route::middleware('auth')->group(function () {
    
    // 存檔 API
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    
    // 💡 2. 新增刪除行程 API
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    // 會員中心預設路由
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';