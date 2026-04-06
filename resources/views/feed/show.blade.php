<x-app-layout>

@push('styles')
<style>
    /* 按讚動畫 */
    @keyframes heart-pop { 0%{transform:scale(1)} 30%{transform:scale(1.45)} 60%{transform:scale(0.9)} 100%{transform:scale(1)} }
    .heart-pop { animation: heart-pop 0.4s cubic-bezier(.36,.07,.19,.97) forwards; }

    /* 留言淡入 */
    @keyframes comment-in { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:translateY(0)} }
    .comment-in { animation: comment-in 0.35s ease forwards; }

    /* 景點卡片圖片 hover */
    .place-img { transition: transform 0.4s ease; }
    .place-card:hover .place-img { transform: scale(1.05); }

    /* 漸層封面遮罩 */
    .cover-overlay { background: linear-gradient(to top, rgba(0,0,0,0.7) 0%, rgba(0,0,0,0.2) 50%, transparent 100%); }
</style>
@endpush

@php
    $itinerary = $post->trip
        ? (is_string($post->trip->itinerary_data)
            ? json_decode($post->trip->itinerary_data, true)
            : $post->trip->itinerary_data)
        : [];

    // 收集所有有照片的景點（最多 5 張）
    $allPhotos = [];
    if ($itinerary) {
        foreach ($itinerary as $places) {
            foreach ($places as $p) {
                if (!empty($p['photo'])) $allPhotos[] = $p['photo'];
                if (count($allPhotos) >= 5) break 2;
            }
        }
    }
    $coverPhoto = $post->image_url ?: ($allPhotos[0] ?? null);
@endphp

{{-- ══════════════════════════════════════
     封面 Hero（照片馬賽克 or 單張大圖）
══════════════════════════════════════ --}}
<div class="relative overflow-hidden bg-slate-900"
     style="height: {{ count($allPhotos) >= 3 ? '420px' : '320px' }}">

    @if(count($allPhotos) >= 3)
        {{-- 多張照片：左大右雙格拼貼 --}}
        <div class="absolute inset-0 grid grid-cols-2 gap-1">
            <div class="overflow-hidden">
                <img src="{{ $allPhotos[0] }}" class="w-full h-full object-cover" alt="">
            </div>
            <div class="grid grid-rows-2 gap-1">
                <div class="overflow-hidden">
                    <img src="{{ $allPhotos[1] }}" class="w-full h-full object-cover" alt="">
                </div>
                <div class="overflow-hidden relative">
                    <img src="{{ $allPhotos[2] }}" class="w-full h-full object-cover" alt="">
                    @if(count($allPhotos) > 3)
                        <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                            <span class="text-white font-extrabold text-2xl">+{{ count($allPhotos) - 3 }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @elseif($coverPhoto)
        <img src="{{ $coverPhoto }}" class="absolute inset-0 w-full h-full object-cover" alt="">
    @else
        <div class="absolute inset-0 bg-gradient-to-br from-slate-800 to-indigo-900 flex items-center justify-center">
            <i class="bi bi-images text-6xl text-white/20"></i>
        </div>
    @endif

    <div class="cover-overlay absolute inset-0"></div>

    {{-- 封面資訊 --}}
    <div class="absolute bottom-0 left-0 right-0 p-6 md:p-10 z-10">
        <div class="max-w-4xl mx-auto">
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="bg-white/20 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full border border-white/30 flex items-center gap-1.5">
                    <i class="bi bi-calendar3"></i> {{ $post->days_count }} 天行程
                </span>
                @if($post->trip_id)
                <span class="bg-violet-500/70 backdrop-blur-sm text-white text-xs font-bold px-3 py-1 rounded-full border border-violet-400/40 flex items-center gap-1">
                    <i class="bi bi-robot"></i> AI 規劃
                </span>
                @endif
            </div>
            <h1 class="text-2xl md:text-4xl font-extrabold text-white leading-tight drop-shadow-lg">{{ $post->title }}</h1>
            <div class="flex items-center gap-3 mt-3">
                <div class="w-8 h-8 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center text-white font-bold text-sm border border-white/30">
                    {{ mb_strtoupper(mb_substr($post->user->name, 0, 1)) }}
                </div>
                <span class="text-white/90 text-sm font-semibold">{{ $post->user->name }}</span>
                <span class="text-white/50 text-xs">{{ $post->created_at->format('Y年m月d日') }}</span>
            </div>
        </div>
    </div>

    {{-- 返回按鈕 --}}
    <div class="absolute top-4 left-4 z-20">
        <a href="{{ route('feed.index') }}" class="inline-flex items-center gap-2 bg-black/30 hover:bg-black/50 backdrop-blur-sm text-white text-sm font-semibold px-4 py-2 rounded-xl border border-white/20 transition">
            <i class="bi bi-arrow-left"></i> 返回社群
        </a>
    </div>
</div>

{{-- ══════════════════════════════════════
     主內容
══════════════════════════════════════ --}}
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-10 pb-24">

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-medium flex items-center gap-2 text-sm">
            <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
        </div>
    @endif

    {{-- 按讚 / 瀏覽 互動列 --}}
    <div class="flex items-center justify-between bg-white rounded-2xl px-5 py-3 shadow-sm border border-slate-100 mb-8">
        <button id="like-btn" onclick="toggleLike({{ $post->id }})"
            class="flex items-center gap-2.5 px-4 py-2 rounded-xl font-bold transition-all text-sm
                   {{ $isLiked ? 'bg-rose-50 text-rose-500 border border-rose-100' : 'bg-slate-50 text-slate-500 border border-slate-200 hover:border-rose-200 hover:text-rose-400' }}">
            <i id="like-icon" class="bi {{ $isLiked ? 'bi-heart-fill' : 'bi-heart' }} text-base"></i>
            <span id="like-count">{{ $post->likes_count }}</span> 個喜歡
        </button>
        <div class="flex items-center gap-4 text-[13px] text-slate-400 font-semibold">
            <span class="flex items-center gap-1.5"><i class="bi bi-eye"></i> {{ $post->views_count }}</span>
            <span class="flex items-center gap-1.5"><i class="bi bi-chat-dots"></i> <span id="comment-count-top">{{ $post->comments_count }}</span></span>
            @if($post->trip_id)
            <a href="/map?trip_id={{ $post->trip_id }}"
               class="inline-flex items-center gap-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold px-4 py-2 rounded-xl text-xs transition shadow-sm">
                <i class="bi bi-map-fill"></i> 載入此行程
            </a>
            @endif
        </div>
    </div>

    {{-- 旅遊心得 --}}
    @if($post->content)
    <div class="bg-white rounded-2xl p-6 md:p-8 shadow-sm border border-slate-100 mb-8">
        <h2 class="text-base font-extrabold text-slate-700 mb-4 flex items-center gap-2">
            <i class="bi bi-pencil-square text-indigo-400"></i> 旅遊心得
        </h2>
        <p class="text-slate-600 leading-relaxed text-[15px] whitespace-pre-line">{{ $post->content }}</p>
    </div>
    @endif

    {{-- 行程路線（含景點照片） --}}
    @if($itinerary && count($itinerary) > 0)
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 mb-8 overflow-hidden">
        <div class="px-6 md:px-8 pt-6 pb-4 border-b border-slate-100">
            <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                <i class="bi bi-geo-alt-fill text-indigo-500"></i> 行程路線規劃
            </h2>
        </div>

        <div class="p-6 md:p-8 space-y-10">
            @foreach($itinerary as $day => $places)
            <div>
                <div class="inline-flex items-center gap-2 bg-slate-800 text-white px-4 py-1.5 rounded-lg text-sm font-bold mb-5 shadow-sm">
                    <i class="bi bi-sun-fill text-amber-300 text-xs"></i> Day {{ $day }}
                </div>

                <div class="space-y-4">
                    @foreach($places as $index => $place)
                    <div class="place-card flex gap-4 p-4 rounded-2xl border border-slate-100 hover:border-indigo-100 hover:bg-indigo-50/30 transition-all group">

                        {{-- 景點照片 --}}
                        <div class="shrink-0 w-20 h-20 md:w-24 md:h-24 rounded-xl overflow-hidden bg-slate-100 border border-slate-200 shadow-sm">
                            @if(!empty($place['photo']))
                                <img src="{{ $place['photo'] }}" alt="{{ $place['name'] ?? '' }}"
                                     class="place-img w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="bi bi-image text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        {{-- 景點資訊 --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2 mb-1">
                                <h4 class="font-extrabold text-slate-800 text-[15px] group-hover:text-indigo-700 transition-colors leading-tight">
                                    {{ $place['name'] ?? '未知地點' }}
                                </h4>
                                <span class="shrink-0 text-[11px] font-bold text-slate-400 bg-slate-50 border border-slate-200 px-2 py-0.5 rounded-md">
                                    #{{ $index + 1 }}
                                </span>
                            </div>

                            @if(!empty($place['rating']))
                            <div class="flex items-center gap-1 text-xs text-amber-500 font-bold mb-1.5">
                                <i class="bi bi-star-fill text-[10px]"></i> {{ $place['rating'] }}
                                @if(!empty($place['types'][0]))
                                <span class="text-slate-300 mx-1">·</span>
                                <span class="text-slate-400 font-semibold">{{ str_replace('_', ' ', $place['types'][0]) }}</span>
                                @endif
                            </div>
                            @endif

                            @if(!empty($place['ai_description']))
                            <p class="text-[12px] text-slate-500 leading-relaxed bg-amber-50/60 border border-amber-100 rounded-lg px-3 py-1.5 flex items-start gap-1.5">
                                <i class="bi bi-lightbulb text-amber-500 shrink-0 mt-0.5"></i>
                                {{ $place['ai_description'] }}
                            </p>
                            @endif

                            @if(!empty($place['stay_time']))
                            <p class="text-[11px] text-slate-400 mt-1.5 flex items-center gap-1">
                                <i class="bi bi-clock text-indigo-300"></i> 建議停留 {{ $place['stay_time'] }}
                            </p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>

        @if($post->trip_id)
        <div class="px-6 md:px-8 py-6 bg-slate-50 border-t border-slate-100 text-center">
            <a href="/map?trip_id={{ $post->trip_id }}"
               class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-700 text-white px-8 py-3 rounded-xl font-bold shadow-sm transition text-sm hover:-translate-y-0.5 hover:shadow-md">
                <i class="bi bi-map"></i> 將此行程載入我的地圖
            </a>
            <p class="text-[11px] text-slate-400 mt-2">載入後可自由修改並存為你的版本</p>
        </div>
        @endif
    </div>
    @endif

    {{-- ══════════════════════════════
         留言區
    ══════════════════════════════ --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-6 md:px-8 pt-6 pb-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-lg font-extrabold text-slate-800 flex items-center gap-2">
                <i class="bi bi-chat-dots-fill text-indigo-500"></i> 留言討論
                <span id="comment-count" class="text-sm font-bold text-slate-400 bg-slate-100 px-2 py-0.5 rounded-full">{{ $post->comments_count }}</span>
            </h2>
        </div>

        {{-- 留言列表 --}}
        <div id="comments-list" class="divide-y divide-slate-50 px-6 md:px-8">
            @forelse($post->comments as $comment)
            <div class="py-5 flex gap-4" id="comment-{{ $comment->id }}">
                <div class="w-9 h-9 shrink-0 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm shadow-sm">
                    {{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-baseline justify-between mb-1">
                        <span class="font-bold text-slate-800 text-sm">{{ $comment->user->name }}</span>
                        <span class="text-[11px] text-slate-400">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    <p class="text-[13px] text-slate-600 leading-relaxed">{{ $comment->content }}</p>
                </div>
                @if(Auth::id() === $comment->user_id)
                <button onclick="deleteComment({{ $comment->id }})"
                    class="shrink-0 text-slate-300 hover:text-rose-400 transition p-1 self-start mt-1 rounded-lg hover:bg-rose-50">
                    <i class="bi bi-trash3 text-xs"></i>
                </button>
                @endif
            </div>
            @empty
            <div id="no-comments" class="text-center py-12">
                <i class="bi bi-chat-square-quote text-4xl text-slate-200 mb-3 block"></i>
                <p class="text-slate-500 font-bold text-sm">成為第一個留言的旅人吧！</p>
            </div>
            @endforelse
        </div>

        {{-- 留言輸入框 --}}
        <div class="px-6 md:px-8 py-6 bg-slate-50/50 border-t border-slate-100">
            @auth
            <div class="flex gap-3">
                <div class="w-9 h-9 shrink-0 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm hidden md:flex">
                    {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 relative">
                    <textarea id="comment-input" rows="2"
                        placeholder="寫下你的讚美或行程建議..."
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 placeholder:text-slate-400 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none transition pr-14 custom-scrollbar"></textarea>
                    <button id="comment-submit-btn" onclick="submitComment()"
                        class="absolute bottom-2.5 right-2.5 bg-indigo-600 hover:bg-indigo-700 text-white w-9 h-9 rounded-xl flex items-center justify-center transition shadow-sm">
                        <i class="bi bi-send-fill text-sm" id="comment-send-icon"></i>
                    </button>
                </div>
            </div>
            @else
            <div class="text-center py-4">
                <p class="text-[13px] text-slate-500 font-semibold mb-3">想參與討論嗎？</p>
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 bg-white text-slate-700 px-6 py-2 rounded-xl border border-slate-200 font-bold shadow-sm hover:bg-slate-50 hover:text-indigo-600 transition text-sm">
                    <i class="bi bi-person-circle text-indigo-400"></i> 登入會員
                </a>
            </div>
            @endauth
        </div>
    </div>

</div>

@push('scripts')
<script>
const POST_ID = {{ $post->id }};
const CSRF   = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// ─── 按讚 ────────────────────────────────────────────
async function toggleLike(postId) {
    @if(!Auth::check())
        if(confirm('請先登入才能按讚！\n是否前往登入頁？')) window.location.href = "{{ route('login') }}";
        return;
    @endif

    const btn   = document.getElementById('like-btn');
    const icon  = document.getElementById('like-icon');
    const count = document.getElementById('like-count');

    // 立即播動畫
    icon.classList.add('heart-pop');
    icon.addEventListener('animationend', () => icon.classList.remove('heart-pop'), { once: true });

    try {
        const res  = await fetch(`/posts/${postId}/like`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF }
        });
        const data = await res.json();

        count.textContent = data.likes_count;

        if (data.status === 'liked') {
            btn.className  = btn.className.replace(/bg-slate-50|text-slate-500|border-slate-200|hover:border-rose-200|hover:text-rose-400/g, '');
            btn.classList.add('bg-rose-50', 'text-rose-500', 'border-rose-100');
            icon.className = 'bi bi-heart-fill text-base';
        } else {
            btn.className  = btn.className.replace(/bg-rose-50|text-rose-500|border-rose-100/g, '');
            btn.classList.add('bg-slate-50', 'text-slate-500', 'border-slate-200', 'hover:border-rose-200', 'hover:text-rose-400');
            icon.className = 'bi bi-heart text-base';
        }
    } catch(e) { console.error('按讚失敗', e); }
}

// ─── 送出留言（AJAX，不重新整理） ────────────────────
async function submitComment() {
    const input  = document.getElementById('comment-input');
    const btn    = document.getElementById('comment-submit-btn');
    const content = input.value.trim();
    if (!content) { input.focus(); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split text-sm animate-spin"></i>';

    try {
        const res  = await fetch(`/posts/${POST_ID}/comments`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ content })
        });

        if (!res.ok) throw new Error('failed');

        const data = await res.json();

        // 移除「沒有留言」提示
        const noComments = document.getElementById('no-comments');
        if (noComments) noComments.remove();

        // 插入新留言到列表最後
        const list = document.getElementById('comments-list');
        const div  = document.createElement('div');
        div.className = 'py-5 flex gap-4 comment-in';
        div.id = `comment-${data.id}`;
        div.innerHTML = `
            <div class="w-9 h-9 shrink-0 bg-indigo-50 border border-indigo-100 rounded-full flex items-center justify-center text-indigo-600 font-bold text-sm shadow-sm">
                ${data.user_initial}
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-baseline justify-between mb-1">
                    <span class="font-bold text-slate-800 text-sm">${data.user_name}</span>
                    <span class="text-[11px] text-slate-400">剛剛</span>
                </div>
                <p class="text-[13px] text-slate-600 leading-relaxed">${escHtml(data.content)}</p>
            </div>
            <button onclick="deleteComment(${data.id})"
                class="shrink-0 text-slate-300 hover:text-rose-400 transition p-1 self-start mt-1 rounded-lg hover:bg-rose-50">
                <i class="bi bi-trash3 text-xs"></i>
            </button>`;
        list.appendChild(div);
        div.scrollIntoView({ behavior: 'smooth', block: 'nearest' });

        // 更新留言數
        const newCount = parseInt(document.getElementById('comment-count').textContent) + 1;
        document.getElementById('comment-count').textContent = newCount;
        document.getElementById('comment-count-top').textContent = newCount;

        input.value = '';
    } catch(e) {
        alert('留言送出失敗，請稍後再試。');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send-fill text-sm" id="comment-send-icon"></i>';
    }
}

// ─── 刪除留言（AJAX） ─────────────────────────────────
async function deleteComment(commentId) {
    if (!confirm('確定要刪除這則留言嗎？')) return;
    try {
        const res = await fetch(`/comments/${commentId}`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ _method: 'DELETE' })
        });
        if (!res.ok) throw new Error('failed');
        const el = document.getElementById(`comment-${commentId}`);
        if (el) { el.style.opacity = '0'; el.style.transform = 'translateX(20px)'; el.style.transition = 'all 0.3s'; setTimeout(() => el.remove(), 300); }
        const newCount = Math.max(0, parseInt(document.getElementById('comment-count').textContent) - 1);
        document.getElementById('comment-count').textContent = newCount;
        document.getElementById('comment-count-top').textContent = newCount;
    } catch(e) { alert('刪除失敗，請稍後再試。'); }
}

// Enter 送出留言（Shift+Enter 換行）
document.getElementById('comment-input')?.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); submitComment(); }
});

function escHtml(str) {
    return str.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
@endpush

</x-app-layout>
