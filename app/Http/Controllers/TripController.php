<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function store(Request $request)
    {
        // 1. 再次確認是否有登入 (雖然前端有擋，但後端防護更安全)
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => '請先登入帳號'], 401);
        }

        // 2. 驗證前端傳進來的資料
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'itinerary_data' => 'required|array', // 前端傳的 JSON 會被自動解析成 array
        ]);

        // 3. 存入資料庫
        $trip = Trip::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'itinerary_data' => $validated['itinerary_data'],
        ]);

        // 4. 回傳成功訊息給前端
        return response()->json([
            'status' => 'success',
            'id' => $trip->id,
            'message' => '行程儲存成功！'
        ]);
    }
}