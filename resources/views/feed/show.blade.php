<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $post->title }} - AI 旅程大師</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-4 shadow-md sticky top-0 z-50">
        <div class="max-w-4xl mx-auto flex justify-between items-center">
            <a href="/feed" class="text-xl font-bold flex items-center gap-2 hover:scale-105 transition">
                <i class="bi bi-arrow-left-circle-fill"></i> 返回動態牆
            </a>
            <div class="flex items-center gap-5 font-bold">
                <a href="/" class="text-blue-200 hover:text-white transition">在地圖規劃</a>
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto p-6 mt-4">
        
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-bold flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8">
            
            @if($post->image_url)
                <img src="{{ $post->image_url }}" alt="封面" class="w-full h-64 md:h-80 object-cover">
            @else
                <div class="w-full h-40 md:h-64 bg-gradient-to-br from-blue-100 to-indigo-100 flex items-center justify-center text-indigo-300">
                    <i class="bi bi-image text-6xl opacity-40"></i>
                </div>
            @endif

            <div class="p-6 md:p-10">
                <div class="mb-8 border-b border-slate-100 pb-6">
                    <div class="flex justify-between items-start mb-4">
                        <h1 class="text-2xl md:text-4xl font-extrabold text-slate-800 leading-tight">{{ $post->title }}</h1>
                        <span class="bg-indigo-50 text-indigo-600 px-3 py-1 rounded-lg text-sm font-black whitespace-nowrap">
                            {{ $post->days_count }} 天行程
                        </span>
                    </div>
                    <div class="flex items-center gap-3 text-slate-500 font-bold text-sm">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white text-lg shadow-sm">
                            {{ mb_substr($post->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-slate-800 text-base">{{ $post->user->name }}</p>
                            <p class="text-xs text-slate-400">{{ $post->created_at->format('Y年m月d日') }} 發佈</p>
                        </div>
                        
                        @if(Auth::id() === $post->user_id)
                            <form action="{{ route('feed.destroy', $post->id) }}" method="POST" class="ml-auto" onsubmit="return confirm('確定要刪除這篇貼文嗎？');">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 bg-red-50 hover:bg-red-100 px-3 py-1.5 rounded-lg transition"><i class="bi bi-trash3-fill"></i> 刪除貼文</button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($post->content)
                    <div class="prose prose-slate max-w-none mb-10 text-slate-600 leading-loose">
                        {!! nl2br(e($post->content)) !!}
                    </div>
                @endif

                @php
                    $itinerary = $post->trip ? (is_string($post->trip->itinerary_data) ? json_decode($post->trip->itinerary_data, true) : $post->trip->itinerary_data) : [];
                @endphp
                
                @if($itinerary && count($itinerary) > 0)
                    <div class="bg-slate-50 rounded-2xl p-6 border border-slate-100 mb-8">
                        <h3 class="text-lg font-extrabold text-slate-800 mb-5 flex items-center gap-2">
                            <i class="bi bi-map-fill text-blue-500"></i> 行程路線規劃
                        </h3>
                        
                        <div class="space-y-6">
                            @foreach($itinerary as $day => $places)
                                <div>
                                    <div class="inline-block bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-bold mb-3 shadow-sm">
                                        Day {{ $day }}
                                    </div>
                                    <div class="pl-4 border-l-2 border-blue-200 space-y-4 ml-2">
                                        @foreach($places as $index => $place)
                                            <div class="relative pl-6">
                                                <div class="absolute w-3 h-3 bg-white border-2 border-blue-500 rounded-full -left-[7px] top-1.5"></div>
                                                <h4 class="font-bold text-slate-800">{{ $place['name'] ?? '未知地點' }}</h4>
                                                @if(!empty($place['ai_description']))
                                                    <p class="text-xs text-slate-500 mt-1 font-bold">{{ $place['ai_description'] }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-6 text-center">
                            <a href="/?trip_id={{ $post->trip_id }}" class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-xl font-bold shadow-md hover:bg-indigo-700 transition">
                                <i class="bi bi-copy"></i> 將此行程載入我的地圖
                            </a>
                        </div>
                    </div>
                @endif

                <div class="flex items-center gap-4 pt-4 border-t border-slate-100">
                    <button id="like-btn" onclick="toggleLike({{ $post->id }})" class="flex items-center gap-2 px-4 py-2 rounded-xl font-bold transition-colors {{ $isLiked ? 'bg-rose-50 text-rose-500' : 'bg-slate-100 text-slate-500 hover:bg-slate-200' }}">
                        <i id="like-icon" class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                        <span id="like-count">{{ $post->likes_count }}</span>
                    </button>
                    <span class="text-slate-400 font-bold text-sm flex items-center gap-1">
                        <i class="bi bi-eye-fill"></i> {{ $post->views_count }} 次瀏覽
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-10 mb-20">
            <h3 class="text-xl font-extrabold text-slate-800 mb-6 flex items-center gap-2">
                <i class="bi bi-chat-dots-fill text-blue-500"></i> 留言討論 ({{ $post->comments_count }})
            </h3>

            <div class="space-y-5 mb-8">
                @forelse($post->comments as $comment)
                    <div class="flex gap-4 p-4 bg-slate-50 rounded-2xl border border-slate-100">
                        <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-slate-300 to-slate-400 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                            {{ mb_substr($comment->user->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <div class="flex items-baseline justify-between mb-1">
                                <h4 class="font-bold text-slate-800">{{ $comment->user->name }}</h4>
                                <span class="text-xs text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <p class="text-sm text-slate-600">{{ $comment->content }}</p>
                        </div>
                        
                        @if(Auth::id() === $comment->user_id)
                            <form action="{{ route('comments.destroy', $comment->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-slate-300 hover:text-red-500 transition"><i class="bi bi-x-lg"></i></button>
                            </form>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 font-bold">
                        成為第一個留言的人吧！
                    </div>
                @endforelse
            </div>

            @auth
                <form action="{{ route('comments.store', $post->id) }}" method="POST" class="flex gap-3">
                    @csrf
                    <div class="w-10 h-10 shrink-0 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white font-bold shadow-sm">
                        {{ mb_substr(auth()->user()->name, 0, 1) }}
                    </div>
                    <div class="flex-1 relative">
                        <textarea name="content" required rows="2" placeholder="寫下你的留言或建議..." class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 outline-none resize-none text-sm pr-20 custom-scrollbar"></textarea>
                        <button type="submit" class="absolute bottom-3 right-3 bg-blue-600 text-white p-2 rounded-lg hover:bg-blue-700 transition shadow-md">
                            <i class="bi bi-send-fill"></i>
                        </button>
                    </div>
                </form>
            @else
                <div class="text-center p-4 bg-slate-100 rounded-xl">
                    <p class="text-sm text-slate-600 font-bold mb-2">想參與討論嗎？</p>
                    <a href="{{ route('login') }}" class="inline-block bg-white text-indigo-600 px-4 py-1.5 rounded-lg border border-slate-200 font-black shadow-sm hover:bg-slate-50 transition">登入會員</a>
                </div>
            @endauth
        </div>
    </main>

    <script>
        async function toggleLike(postId) {
            @if(!Auth::check())
                alert("請先登入才能按讚喔！");
                window.location.href = "{{ route('login') }}";
                return;
            @endif

            try {
                const response = await fetch(`/posts/${postId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });
                
                const data = await response.json();
                
                const btn = document.getElementById('like-btn');
                const icon = document.getElementById('like-icon');
                const countSpan = document.getElementById('like-count');

                countSpan.innerText = data.likes_count;

                if (data.status === 'liked') {
                    btn.classList.replace('bg-slate-100', 'bg-rose-50');
                    btn.classList.replace('text-slate-500', 'text-rose-500');
                    btn.classList.remove('hover:bg-slate-200');
                    icon.classList.replace('bi-heart', 'bi-heart-fill');
                } else {
                    btn.classList.replace('bg-rose-50', 'bg-slate-100');
                    btn.classList.replace('text-rose-500', 'text-slate-500');
                    btn.classList.add('hover:bg-slate-200');
                    icon.classList.replace('bi-heart-fill', 'bi-heart');
                }
            } catch (error) {
                console.error("按讚失敗", error);
            }
        }
    </script>
</body>
</html>