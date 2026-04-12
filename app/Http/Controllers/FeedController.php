<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use App\Models\PostLike;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedController extends Controller
{
    // 1. 顯示動態牆首頁
    public function index()
    {
        // 抓取所有貼文，並附帶作者、行程、留言數、按讚數，依照最新時間排序
        $posts = Post::with(['user', 'trip'])->withCount(['comments', 'likes'])->latest()->paginate(15);
        
        return view('feed.index', compact('posts'));
    }

    // 2. 查看單一貼文詳情
    public function show(Post $post)
    {
        // 增加瀏覽次數
        $post->increment('views_count');
        
        // 載入關聯資料：作者、行程、留言(含留言者)
        $post->load(['user', 'trip', 'comments.user']);
        
        // 檢查當前使用者是否已按讚
        $isLiked = Auth::check() ? $post->likes()->where('user_id', Auth::id())->exists() : false;

        return view('feed.show', compact('post', 'isLiked'));
    }

    // 3. 發佈新貼文
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:100',
            'content' => 'nullable|string|max:2000',
            'trip_id' => 'required|exists:trips,id',
        ]);

        $daysCount = $request->input('days_count', 1);

        // 自動從行程景點資料中擷取封面照片
        $imageUrl = $request->image_url ?: null;
        if (empty($imageUrl)) {
            $trip = Trip::find($request->trip_id);
            if ($trip && $trip->itinerary_data) {
                $itinerary = is_array($trip->itinerary_data)
                    ? $trip->itinerary_data
                    : json_decode($trip->itinerary_data, true);
                foreach ((array) $itinerary as $places) {
                    foreach ((array) $places as $place) {
                        if (!empty($place['photo'])) {
                            $imageUrl = $place['photo'];
                            break 2;
                        }
                    }
                }
            }
        }

        $post = Post::create([
            'user_id'   => Auth::id(),
            'trip_id'   => $request->trip_id,
            'title'     => $request->title,
            'content'   => $request->content,
            'image_url' => $imageUrl,
            'days_count' => $daysCount,
        ]);

        return redirect()->route('feed.index')->with('success', '🎉 貼文發佈成功！');
    }

    // 4. 刪除貼文 (限作者)
    public function destroy(Post $post)
    {
        if ($post->user_id !== Auth::id()) {
            abort(403, '這不是你的貼文喔！');
        }

        $post->delete();
        return redirect()->route('feed.index')->with('success', '🗑️ 貼文已刪除。');
    }

    // 5. 新增留言
    public function storeComment(Request $request, Post $post)
    {
        $request->validate([
            'content' => 'required|string|max:500',
        ]);

        $comment = Comment::create([
            'user_id' => Auth::id(),
            'post_id' => $post->id,
            'content' => $request->content,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'           => $comment->id,
                'content'      => $comment->content,
                'user_name'    => Auth::user()->name,
                'user_initial' => mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)),
            ]);
        }

        return back()->with('success', '💬 留言成功！');
    }

    // 6. 刪除留言 (限作者)
    public function destroyComment(Comment $comment)
    {
        if ($comment->user_id !== Auth::id()) {
            abort(403, '無法刪除別人的留言！');
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json(['status' => 'deleted']);
        }

        return back()->with('success', '🗑️ 留言已刪除。');
    }

    // 7. 按讚 / 取消按讚 (API 呼叫用)
    public function toggleLike(Post $post)
    {
        $userId = Auth::id();
        $like = PostLike::where('post_id', $post->id)->where('user_id', $userId)->first();

        if ($like) {
            // 如果已經按過讚，就取消
            $like->delete();
            $status = 'unliked';
        } else {
            // 如果沒按過，就新增
            PostLike::create(['post_id' => $post->id, 'user_id' => $userId]);
            $status = 'liked';
        }

        return response()->json([
            'status' => $status,
            'likes_count' => $post->likes()->count()
        ]);
    }
}