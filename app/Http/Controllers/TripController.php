<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TripController extends Controller
{
    public function store(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => '請先登入帳號'], 401);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'itinerary_data' => 'required|array', 
        ]);

        $trip = Trip::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'itinerary_data' => $validated['itinerary_data'],
        ]);

        return response()->json([
            'status' => 'success',
            'id' => $trip->id,
            'message' => '行程儲存成功！'
        ]);
    }

    // 💡 新增這段：刪除行程功能
    public function destroy(Trip $trip)
    {
        // 防駭客：確定這個行程真的是目前登入者的
        if ($trip->user_id !== Auth::id()) {
            abort(403, '無權限刪除此行程');
        }

        $trip->delete();

        return back()->with('success', '行程已成功刪除！');
    }
}