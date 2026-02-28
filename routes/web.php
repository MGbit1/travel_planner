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

// 💡 接收前端存檔的路由
Route::post('/trips', function(Request $request) {
    $trip = Trip::create([
        'title' => $request->input('title', '未命名行程'),
        'itinerary_data' => $request->input('itinerary_data')
    ]);

    return response()->json([
        'status' => 'success',
        'id' => $trip->id
    ]);
});

// 🤖 AI 智慧行程生成通道
Route::post('/ai-generate', [MapController::class, 'generateAI']);