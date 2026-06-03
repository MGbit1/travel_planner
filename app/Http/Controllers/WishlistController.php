<?php

namespace App\Http\Controllers;

use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WishlistController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'place_name' => 'required|string|max:255',
            'address'    => 'nullable|string|max:500',
            'latitude'   => 'required|numeric',
            'longitude'  => 'required|numeric',
            'rating'     => 'nullable|numeric',
            'image_url'  => 'nullable|string|max:1000',
        ]);

        [$wishlist, $created] = [
            Wishlist::firstOrCreate(
                ['user_id' => Auth::id(), 'place_name' => $validated['place_name']],
                array_merge($validated, ['user_id' => Auth::id()])
            ),
            false,
        ];
        $created = $wishlist->wasRecentlyCreated;

        return response()->json([
            'status'  => $created ? 'success' : 'already_exists',
            'id'      => $wishlist->id,
            'message' => $created ? '已加入收藏！' : '已在收藏清單中',
        ]);
    }

    public function destroy(Wishlist $wishlist)
    {
        if ($wishlist->user_id !== Auth::id()) {
            abort(403);
        }

        $wishlist->delete();

        if (request()->expectsJson()) {
            return response()->json(['status' => 'deleted']);
        }

        return back()->with('success', '已從收藏移除');
    }
}
