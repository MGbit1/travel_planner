<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('feed.index') }}" class="text-slate-500 hover:text-indigo-600 transition-colors flex items-center gap-2 text-sm font-semibold">
                <i class="bi bi-arrow-left"></i> 返回社群
            </a>
            <div class="h-6 w-px bg-slate-300"></div>
            <h2 class="font-semibold text-lg text-slate-800 leading-tight truncate">
                {{ $post->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 mb-20">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-medium flex items-center gap-2 text-sm">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden mb-8">
                
                <div class="relative h-64 md:h-80 bg-slate-50 border-b border-slate-100 overflow-hidden">
                    @if($post->image_url)
                        <img src="{{ $post->image_url }}" alt="封面" class="w-full h-full object-cover">
                    @else
                        <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-100/50">
                            <i class="bi bi-images text-5xl mb-3 opacity-40"></i>
                            <span class="text-xs font-semibold tracking-widest uppercase opacity-60">TripFlow Journey</span>
                        </div>
                    @endif
                    <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-sm text-slate-700 px-3 py-1.5 rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 border border-slate-200">
                        <i class="bi bi-calendar3 text-indigo-500"></i> {{ $post->days_count }} 天行程
                    </div>
                </div>

                <div class="p-6 md:p-10">
                    <div class="mb-8 border-b border-slate-100 pb-8">
                        <h1 class="text-3xl md:text-4xl font-extrabold text-slate-800 leading-tight mb-6">{{ $post->title }}</h1>
                        
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3.5">
                                <div class="w-11 h-11 bg-indigo-50 rounded-full flex items-center justify-center text-indigo-600 text-lg font-bold border border-indigo-100 shadow-sm">
                                    {{ mb_strtoupper(mb_substr($post->user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-slate-800 font-bold text-sm">{{ $post->user->name }}</p>
                                    <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ $post->created_at->format('Y年m月d日') }} 發佈</p>
                                </div>
                            </div>
                            
                            @if(Auth::id() === $post->user_id)
                                <form action="{{ route('feed.destroy', $post->id) }}" method="POST" onsubmit="return confirm('確定要刪除這篇貼文嗎？刪除後無法復原喔！');">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 hover:text-rose-600 bg-rose-50 hover:bg-rose-100 px-3.5 py-2 rounded-xl transition text-sm font-semibold flex items-center gap-1.5">
                                        <i class="bi bi-trash3"></i> 刪除貼文
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    @if($post->content)
                        <div class="prose prose-slate prose-p:leading-relaxed max-w-none mb-12 text-slate-600 font-medium text-[15px]">
                            {!! nl2br(e($post->content)) !!}
                        </div>
                    @endif

                    @php
                        $itinerary = $post->trip ? (is_string($post->trip->itinerary_data) ? json_decode($post->trip->itinerary_data, true) : $post->trip->itinerary_data) : [];
                    @endphp
                    
                    @if($itinerary && count($itinerary) > 0)
                        <div class="bg-slate-50/50 rounded-2xl p-6 md:p-8 border border-slate-100 mb-8">
                            <h3 class="text-lg font-extrabold text-slate-800 mb-6 flex items-center gap-2">
                                <i class="bi bi-geo-alt-fill text-indigo-500"></i> 行程路線規劃
                            </h3>
                            
                            <div class="space-y-8">
                                @foreach($itinerary as $day => $places)
                                    <div>
                                        <div class="inline-block bg-slate-800 text-white px-3.5 py-1.5 rounded-lg text-sm font-bold mb-4 shadow-sm">
                                            Day {{ $day }}
                                        </div>
                                        
                                        <div class="pl-3 md:pl-4 border-l-2 border-slate-200 space-y-5 ml-2.5">
                                            @foreach($places as $index => $place)
                                                <div class="relative pl-6 md:pl-8 group">
                                                    <div class="absolute w-3.5 h-3.5 bg-white border-2 border-indigo-400 rounded-full -left-[9px] top-1.5 group-hover:border-indigo-600 transition-colors shadow-sm"></div>
                                                    
                                                    <h4 class="font-bold text-slate-800 text-[15px] group-hover:text-indigo-600 transition-colors">{{ $place['name'] ?? '未知地點' }}</h4>
                                                    
                                                    @if(!empty($place['ai_description']))
                                                        <p class="text-[12px] text-slate-500 mt-1.5 font-medium flex items-start gap-1.5 bg-white px-3 py-2 rounded-lg border border-slate-100 shadow-sm inline-block">
                                                            <i class="bi bi-lightbulb text-amber-500 mt-0.5"></i> {{ $place['ai_description'] }}
                                                        </p>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mt-10 text-center border-t border-slate-200/60 pt-8">
                                <a href="/?trip_id={{ $post->trip_id }}" class="inline-flex bg-slate-800 text-white px-8 py-3.5 rounded-xl font-bold shadow-sm hover:bg-slate-700 hover:shadow-md hover:-translate-y-0.5 transition-all items-center gap-2 text-sm">
                                    <i class="bi bi-map"></i> 將此行程載入我的地圖
                                </a>
                                <p class="text-[11px] text-slate-400 mt-3 font-medium">點擊後可於地圖上自由修改並存為您的版本</p>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pt-6 border-t border-slate-100">
                        <button id="like-btn" onclick="toggleLike({{ $post->id }})" class="flex items-center gap-2 px-4 py-2 rounded-xl font-bold transition-colors shadow-sm border border-transparent {{ $isLiked ? 'bg-rose-50 text-rose-500 border-rose-100' : 'bg-white text-slate-500 border-slate-200 hover:bg-slate-50' }}">
                            <i id="like-icon" class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                            <span id="like-count">{{ $post->likes_count }}</span>
                        </button>
                        <span class="text-slate-400 font-bold text-[13px] flex items-center gap-1.5 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-100">
                            <i class="bi bi-eye"></i> {{ $post->views_count }} 次瀏覽
                        </span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 md:p-10">
                <h3 class="text-xl font-extrabold text-slate-800 mb-8 flex items-center gap-2">
                    <i class="bi bi-chat-dots text-indigo-500"></i> 留言討論 ({{ $post->comments_count }})
                </h3>

                <div class="space-y-4 mb-8">
                    @forelse($post->comments as $comment)
                        <div class="flex gap-4 p-4 md:p-5 bg-slate-50/50 rounded-2xl border border-slate-100 hover:bg-slate-50 transition">
                            <div class="w-10 h-10 shrink-0 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-600 font-bold shadow-sm text-sm">
                                {{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-baseline justify-between mb-1.5">
                                    <h4 class="font-bold text-slate-800 text-sm truncate pr-2">{{ $comment->user->name }}</h4>
                                    <span class="text-[11px] text-slate-400 font-medium shrink-0">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[13px] text-slate-600 font-medium leading-relaxed">{{ $comment->content }}</p>
                            </div>
                            
                            @if(Auth::id() === $comment->user_id)
                                <form action="{{ route('comments.destroy', $comment->id) }}" method="POST" class="shrink-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-slate-300 hover:text-rose-500 transition p-1" title="刪除留言"><i class="bi bi-x-lg"></i></button>
                                </form>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-10 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <i class="bi bi-chat-square-quote text-3xl text-slate-300 mb-2 block"></i>
                            <p class="text-slate-500 font-bold text-sm">成為第一個留言的人吧！</p>
                        </div>
                    @endforelse
                </div>

                @auth
                    <form action="{{ route('comments.store', $post->id) }}" method="POST" class="flex gap-3 md:gap-4 mt-8 pt-6 border-t border-slate-100">
                        @csrf
                        <div class="w-10 h-10 shrink-0 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold shadow-sm text-sm hidden md:flex">
                            {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                        </div>
                        <div class="flex-1 relative">
                            <textarea name="content" required rows="2" placeholder="寫下你的讚美或行程建議..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none text-sm pr-16 custom-scrollbar transition font-medium text-slate-800 placeholder:text-slate-400"></textarea>
                            <button type="submit" class="absolute bottom-3 right-3 bg-slate-800 text-white w-9 h-9 rounded-lg hover:bg-slate-700 transition shadow-sm flex items-center justify-center">
                                <i class="bi bi-send-fill text-sm"></i>
                            </button>
                        </div>
                    </form>
                @else
                    <div class="text-center p-6 bg-slate-50/50 rounded-2xl border border-slate-200 mt-8">
                        <p class="text-[13px] text-slate-500 font-bold mb-3">想參與這趟旅程的討論嗎？</p>
                        <a href="{{ route('login') }}" class="inline-block bg-white text-slate-700 px-6 py-2 rounded-xl border border-slate-200 font-bold shadow-sm hover:bg-slate-50 hover:text-indigo-600 transition text-sm">
                            登入會員
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

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

                // 配合新的 Tailwind classes 做切換
                if (data.status === 'liked') {
                    btn.classList.remove('bg-white', 'text-slate-500', 'border-slate-200', 'hover:bg-slate-50');
                    btn.classList.add('bg-rose-50', 'text-rose-500', 'border-rose-100');
                    icon.classList.replace('bi-heart', 'bi-heart-fill');
                } else {
                    btn.classList.remove('bg-rose-50', 'text-rose-500', 'border-rose-100');
                    btn.classList.add('bg-white', 'text-slate-500', 'border-slate-200', 'hover:bg-slate-50');
                    icon.classList.replace('bi-heart-fill', 'bi-heart');
                }
            } catch (error) {
                console.error("按讚失敗", error);
            }
        }
    </script>
</x-app-layout>