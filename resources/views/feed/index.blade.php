<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-slate-800 leading-tight flex items-center gap-2">
            <i class="bi bi-compass text-indigo-500"></i> {{ __('靈感社群') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-3">探索全世界的完美旅程</h1>
                <p class="text-slate-500 font-medium">看看 TripFlow 社群中的旅人們都在去哪裡玩，尋找你的下一趟旅行靈感！</p>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium border border-emerald-200 flex items-center gap-2 shadow-sm max-w-2xl mx-auto">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            @if($posts && $posts->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($posts as $post)
                        <a href="/?trip_id={{ $post->trip_id }}" class="block group bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-sm hover:shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:-translate-y-1 transition-all duration-300 flex flex-col h-full relative">
                            
                            <div class="relative h-48 bg-slate-50 border-b border-slate-100 overflow-hidden">
                                @if($post->image_url)
                                    <img src="{{ $post->image_url }}" alt="{{ $post->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @else
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-slate-300 bg-slate-100/50 group-hover:scale-105 transition-transform duration-500">
                                        <i class="bi bi-images text-3xl mb-2 opacity-40"></i>
                                        <span class="text-[10px] font-semibold tracking-widest uppercase opacity-60">TripFlow</span>
                                    </div>
                                @endif
                                
                                <div class="absolute top-3 left-3 bg-white/95 backdrop-blur-sm text-slate-700 px-2.5 py-1 rounded-lg text-[11px] font-bold shadow-sm flex items-center gap-1.5 border border-slate-200">
                                    <i class="bi bi-calendar3 text-indigo-500"></i> {{ $post->days_count ?? 1 }} 天行程
                                </div>
                            </div>

                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="text-lg font-bold text-slate-800 mb-2 line-clamp-1 group-hover:text-indigo-600 transition-colors">{{ $post->title }}</h3>
                                <p class="text-[13px] text-slate-500 line-clamp-2 leading-relaxed mb-4 flex-1 font-medium">
                                    {{ $post->content ?: '作者沒有留下文字說明，點擊直接查看詳細行程地圖！' }}
                                </p>
                                
                                <div class="flex items-center justify-between pt-4 border-t border-slate-100 mt-auto">
                                    <div class="flex items-center gap-2.5">
                                        <div class="w-6 h-6 rounded-full bg-indigo-50 text-indigo-600 flex items-center justify-center text-[10px] font-bold border border-indigo-100">
                                            {{ mb_strtoupper(mb_substr($post->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="text-[12px] font-semibold text-slate-600 truncate max-w-[100px]">{{ $post->user->name ?? 'TripFlow 用戶' }}</span>
                                    </div>
                                    
                                    <div class="flex items-center gap-3.5 text-[11px] text-slate-400 font-semibold">
                                        <span class="flex items-center gap-1 hover:text-rose-500 transition"><i class="bi bi-heart-fill text-slate-300 group-hover:text-rose-400 transition-colors"></i> {{ $post->likes_count ?? 0 }}</span>
                                        <span class="flex items-center gap-1"><i class="bi bi-chat-fill text-slate-300"></i> {{ $post->comments_count ?? 0 }}</span>
                                        <span class="flex items-center gap-1"><i class="bi bi-eye-fill text-slate-300"></i> {{ $post->views_count ?? 0 }}</span>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                
                <div class="mt-12">
                    {{ $posts->links() }}
                </div>

            @else
                <div class="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-300 shadow-sm max-w-2xl mx-auto">
                    <div class="bg-slate-50 w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100">
                        <i class="bi bi-compass text-2xl text-slate-400"></i>
                    </div>
                    <p class="text-slate-700 text-sm font-bold">目前還沒有人分享行程</p>
                    <p class="text-slate-400 text-xs mt-1.5 font-medium">前往控制台發佈你的第一篇旅程吧！</p>
                    <a href="{{ route('dashboard') }}" class="inline-block mt-6 bg-slate-800 text-white px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-700 transition shadow-sm">
                        前往我的行程
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>