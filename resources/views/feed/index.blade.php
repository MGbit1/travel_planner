<x-app-layout>

    {{-- Hero Header --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-rose-900 pt-14 pb-24 px-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image:url('https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?auto=format&fit=crop&w=1920&q=60'); background-size:cover; background-position:center;"></div>
        <div class="relative z-10">
            <span class="inline-flex items-center gap-2 bg-rose-400/20 text-rose-300 text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full border border-rose-400/30 mb-5">
                <i class="bi bi-compass-fill"></i> 靈感社群
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-3">探索全世界的完美旅程</h1>
            <p class="text-slate-400 text-base md:text-lg max-w-xl mx-auto">看看 TripFlow 旅人都在去哪裡，尋找你的下一趟旅行靈感！</p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 lg:px-8 -mt-10 pb-24">

        {{-- 成功通知 --}}
        @if(session('success'))
            <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium border border-emerald-200 flex items-center gap-2 shadow-sm max-w-2xl mx-auto">
                <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
            </div>
        @endif

        {{-- 工具列 --}}
        <div class="flex items-center justify-between mb-8">
            <p class="text-slate-500 text-sm font-medium">
                @if($posts && $posts->total() > 0)
                    共 {{ $posts->total() }} 篇旅遊分享
                @endif
            </p>
            @auth
            <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-xl shadow-sm transition">
                <i class="bi bi-plus-lg"></i> 分享我的行程
            </a>
            @endauth
        </div>

        @if($posts && $posts->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($posts as $post)
                    {{-- 使用 div+onclick 讓作者刪除按鈕可正常放置 --}}
                    <div class="group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col h-full cursor-pointer relative"
                         onclick="if(!event.target.closest('form,button')) window.location.href='{{ route('feed.show', $post->id) }}'">

                        <div class="relative h-48 bg-slate-100 overflow-hidden">
                            @if($post->image_url)
                                <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            @else
                                <div class="absolute inset-0 flex flex-col items-center justify-center bg-gradient-to-br from-slate-100 to-slate-200 group-hover:from-indigo-50 group-hover:to-slate-100 transition-colors duration-500">
                                    <i class="bi bi-images text-3xl text-slate-300 mb-2"></i>
                                    <span class="text-[10px] font-bold tracking-widest uppercase text-slate-400">TripFlow</span>
                                </div>
                            @endif
                            <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm text-slate-700 px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-sm flex items-center gap-1.5 border border-slate-200">
                                <i class="bi bi-calendar3 text-indigo-500"></i> {{ $post->days_count ?? 1 }} 天行程
                            </div>
                            {{-- AI 生成標籤 --}}
                            @if($post->trip_id)
                            <div class="absolute top-3 right-3 bg-violet-600/90 backdrop-blur-sm text-white px-2 py-1 rounded-lg text-[10px] font-bold flex items-center gap-1">
                                <i class="bi bi-robot"></i> AI 規劃
                            </div>
                            @endif
                        </div>

                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="text-base font-bold text-slate-800 mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ $post->title }}</h3>
                            <p class="text-[13px] text-slate-500 line-clamp-2 leading-relaxed mb-4 flex-1">
                                {{ $post->content ?: '作者沒有留下文字說明，點擊直接查看詳細行程地圖！' }}
                            </p>

                            <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-auto">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold border border-indigo-100">
                                        {{ mb_strtoupper(mb_substr($post->user->name ?? 'U', 0, 1)) }}
                                    </div>
                                    <span class="text-[12px] font-semibold text-slate-600 truncate max-w-[90px]">{{ $post->user->name ?? 'TripFlow 用戶' }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-[11px] text-slate-400 font-semibold">
                                    <span class="flex items-center gap-1"><i class="bi bi-heart-fill text-rose-300 group-hover:text-rose-400 transition-colors"></i> {{ $post->likes_count ?? 0 }}</span>
                                    <span class="flex items-center gap-1"><i class="bi bi-chat-fill text-slate-300"></i> {{ $post->comments_count ?? 0 }}</span>
                                    <span class="flex items-center gap-1"><i class="bi bi-eye-fill text-slate-300"></i> {{ $post->views_count ?? 0 }}</span>
                                    @auth
                                    @if(Auth::id() === $post->user_id)
                                    <form action="{{ route('feed.destroy', $post->id) }}" method="POST"
                                          onsubmit="return confirm('確定要刪除這篇貼文嗎？')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-slate-300 hover:text-red-500 p-1 rounded-lg hover:bg-red-50 transition"
                                            title="刪除貼文">
                                            <i class="bi bi-trash3 text-xs"></i>
                                        </button>
                                    </form>
                                    @endif
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>

        @else
            <div class="text-center py-24 bg-white rounded-3xl border border-dashed border-slate-200 shadow-sm">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                    <i class="bi bi-compass text-2xl text-slate-400"></i>
                </div>
                <p class="text-slate-700 text-base font-bold mb-2">目前還沒有人分享行程</p>
                <p class="text-slate-400 text-sm mb-8">前往地圖規劃你的第一趟旅程，再分享給大家吧！</p>
                <a href="/map" class="inline-flex items-center gap-2 bg-slate-800 text-white px-8 py-3 rounded-xl text-sm font-semibold hover:bg-slate-700 transition shadow-sm">
                    <i class="bi bi-map-fill"></i> 前往探索地圖
                </a>
            </div>
        @endif

    </div>

</x-app-layout>
