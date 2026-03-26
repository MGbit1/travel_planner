<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>社群動態牆 - AI 旅程大師</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-slate-50 font-sans text-slate-800">

    <nav class="bg-gradient-to-r from-blue-600 to-indigo-700 text-white p-4 shadow-md sticky top-0 z-50">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <a href="/" class="text-xl font-bold flex items-center gap-2 hover:scale-105 transition">
                <span>🚀</span> AI 旅程大師
            </a>
            <div class="flex items-center gap-5">
                <a href="/feed" class="font-bold border-b-2 border-white pb-1">🌍 社群動態</a>
                <a href="/ranking" class="text-white hover:text-blue-200 transition font-bold">🏆 景點排行</a>
                <a href="/" class="text-blue-200 hover:text-white transition font-bold">在地圖規劃</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-indigo-600 px-3 py-1.5 rounded-lg text-sm font-black shadow-sm hover:bg-blue-50 transition">我的控制台</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-blue-100 hover:text-white transition">登入/註冊</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto p-6 mt-6">
        
        <div class="mb-10">
            <h1 class="text-3xl font-extrabold text-slate-800 flex items-center gap-2">
                <i class="bi bi-globe-americas text-blue-600"></i> 探索旅程
            </h1>
            <p class="text-slate-500 mt-2 font-bold">看看大家都在去哪裡玩，尋找你的下一趟旅行靈感！</p>
        </div>

        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-bold flex items-center gap-2">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($posts as $post)
                <a href="{{ route('feed.show', $post->id) }}" class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col">
                    
                    <div class="h-48 bg-slate-200 relative overflow-hidden">
                        @if($post->image_url)
                            <img src="{{ $post->image_url }}" alt="封面" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full bg-gradient-to-br from-indigo-100 to-purple-100 flex items-center justify-center text-indigo-300 group-hover:scale-105 transition duration-500">
                                <i class="bi bi-image text-5xl opacity-50"></i>
                            </div>
                        @endif
                        <div class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-lg text-xs font-black text-indigo-700 shadow-sm border border-white/50">
                            <i class="bi bi-calendar-event"></i> {{ $post->days_count }} 天行程
                        </div>
                    </div>

                    <div class="p-5 flex-1 flex flex-col">
                        <h2 class="text-lg font-bold text-slate-800 mb-2 line-clamp-2 group-hover:text-blue-600 transition">{{ $post->title }}</h2>
                        <p class="text-sm text-slate-500 line-clamp-3 mb-4 flex-1">{{ $post->content ?: '作者沒有留下文字說明，點擊查看詳細行程地圖！' }}</p>
                        
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 text-xs text-slate-400 font-bold">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-full flex items-center justify-center text-white text-[10px] shadow-sm">
                                    {{ mb_substr($post->user->name, 0, 1) }}
                                </div>
                                <span class="truncate max-w-[80px]">{{ $post->user->name }}</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="flex items-center gap-1 text-rose-500 bg-rose-50 px-1.5 py-0.5 rounded"><i class="bi bi-heart-fill"></i> {{ $post->likes_count }}</span>
                                <span class="flex items-center gap-1 text-blue-500 bg-blue-50 px-1.5 py-0.5 rounded"><i class="bi bi-chat-dots-fill"></i> {{ $post->comments_count }}</span>
                                <span class="flex items-center gap-1"><i class="bi bi-eye-fill"></i> {{ $post->views_count }}</span>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-20 bg-white rounded-2xl border-2 border-dashed border-slate-200">
                    <i class="bi bi-journal-x text-5xl text-slate-300 mb-3 block"></i>
                    <p class="text-slate-600 text-lg font-bold">目前還沒有人發佈行程喔！</p>
                    <p class="text-sm text-slate-400 mt-2">趕快去地圖規劃你的專屬旅程，並成為第一個分享的大師吧！</p>
                    <a href="/" class="inline-block mt-4 bg-blue-600 text-white px-5 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-md">去規劃行程</a>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $posts->links() }}
        </div>

    </main>
</body>
</html>