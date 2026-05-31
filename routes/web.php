<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\WishlistController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\RankingController;

// 首頁（Landing Page）
Route::get('/', [LandingController::class, 'index'])->name('landing');

// 地圖規劃頁
Route::get('/map', function () {
    $loadedTrip = null;
    if (request()->has('trip_id') && auth()->check()) {
        $loadedTrip = \App\Models\Trip::where('id', request()->query('trip_id'))
                        ->where('user_id', auth()->id())
                        ->first();
    }
    $wishlistItems = auth()->check()
        ? \App\Models\Wishlist::where('user_id', auth()->id())->get(['id', 'place_name', 'latitude', 'longitude', 'image_url', 'rating'])
        : collect();
    return view('welcome', compact('loadedTrip', 'wishlistItems'));
})->name('map');

Route::post('/ai-generate', [MapController::class, 'generateAI'])->middleware('auth');

// Dashboard：行程 + 收藏清單
Route::get('/dashboard', function () {
    $trips     = \App\Models\Trip::where('user_id', auth()->id())->latest()->get();
    $wishlists = \App\Models\Wishlist::where('user_id', auth()->id())->latest()->get();
    return view('dashboard', compact('trips', 'wishlists'));
})->middleware(['auth', 'verified'])->name('dashboard');

// 必須登入後才能使用的功能
Route::middleware('auth')->group(function () {
    Route::post('/trips', [TripController::class, 'store'])->name('trips.store');
    Route::delete('/trips/{trip}', [TripController::class, 'destroy'])->name('trips.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 收藏清單
    Route::post('/wishlist', [WishlistController::class, 'store'])->name('wishlist.store');
    Route::delete('/wishlist/{wishlist}', [WishlistController::class, 'destroy'])->name('wishlist.destroy');

    // 複製行程
    Route::post('/posts/{post}/copy', [FeedController::class, 'copy'])->name('posts.copy');
});

// 動態牆與排行榜（公開瀏覽）
Route::get('/feed', [FeedController::class, 'index'])->name('feed.index');
Route::get('/posts/{post}', [FeedController::class, 'show'])->name('feed.show');
Route::get('/ranking', [RankingController::class, 'index'])->name('ranking.index');

// 需要登入才能操作（發文、留言、按讚）
Route::middleware('auth')->group(function () {
    Route::post('/posts', [FeedController::class, 'store'])->name('feed.store');
    Route::delete('/posts/{post}', [FeedController::class, 'destroy'])->name('feed.destroy');
    Route::post('/posts/{post}/comments', [FeedController::class, 'storeComment'])->name('comments.store');
    Route::delete('/comments/{comment}', [FeedController::class, 'destroyComment'])->name('comments.destroy');
    Route::post('/posts/{post}/like', [FeedController::class, 'toggleLike'])->name('posts.like');
});


require __DIR__.'/auth.php';
