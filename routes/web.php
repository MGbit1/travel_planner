<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController; 
use App\Http\Controllers\TripController; 
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\RankingController;

// 💡 修改這段：如果網址有 trip_id，就把對應的行程撈出來傳給首頁
Route::get('/', function () {
    $loadedTrip = null;
    
    // 確認有傳入 trip_id 參數，而且使用者有登入
    if (request()->has('trip_id') && auth()->check()) {
        // 去資料庫找這筆行程 (加上 user_id 防護，確保只能載入自己的)
        $loadedTrip = \App\Models\Trip::where('id', request()->query('trip_id'))
                        ->where('user_id', auth()->id())
                        ->first();
    }
    
    return view('welcome', compact('loadedTrip'));
});

Route::post('/ai-generate', [MapController::class, 'generateAI']);

// 把使用者的行程撈出來傳給視圖
Route::get('/dashboard', function () {
    $trips = \App\Models\Trip::where('user_id', auth()->id())->latest()->get();
    return view('dashboard', compact('trips'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 必須「登入後」才能使用的功能群組
Route::middleware('auth')->group(function () {
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// 💡 動態牆與排行榜 (公開瀏覽)
Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
Route::get('/posts/{post}', [FeedController::class, 'show'])->name('feed.show');
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

// 💡 需要登入才能操作的功能 (發文、留言、按讚)
Route::middleware('auth')->group(function () {
    // 發文
    Route::post('/posts', [FeedController::class, 'store'])->name('feed.store');
    // 刪除貼文 (限作者)
    Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('feed.destroy');
    
    // 留言
    Route::post('/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');
    // 刪除留言 (限作者)
    Route::delete('/comments/{comment}', [FeedController::class, 'destroyComment'])->name('comments.destroy');
    
    // 按讚/取消按讚 (API)
    Route::post('/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
});


require __DIR__.'/auth.php';