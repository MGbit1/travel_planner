<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class TripController extends Controller
{
    public function store(Request $request)
    {
        // 1. 驗證資料
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'itinerary_data' => 'required|array',
        ]);

        // 2. 存入資料庫
        $trip = Trip::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => '行程儲存成功！',
            'id' => $trip->id
        ]);
    }
}