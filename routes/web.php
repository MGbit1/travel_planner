<?php

use App\Http\Controllers\MapController;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Trip;

// 首頁與其他您原本的地圖路由
Route::get('/', [MapController::class, 'index']);
Route::post('/places', [MapController::class, 'store']);
Route::delete('/places/{id}', [MapController::class, 'destroy']);
Route::get('/ai-plan', [MapController::class, 'aiPlan']);

// 💡 接收前端存檔的路由 (保留這一個就好！)
Route::post('/trips', function(Request $request) {
    // 驗證並儲存
    $trip = Trip::create([
        'title' => $request->input('title', '未命名行程'),
        'itinerary_data' => $request->input('itinerary_data')
    ]);

    // 回傳成功訊息給前端
    return response()->json([
        'status' => 'success',
        'id' => $trip->id
    ]);
});