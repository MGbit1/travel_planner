<x-app-layout>

{{-- AOS + 自訂樣式 --}}
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
<style>
    /* Hero */
    .hero-slide { position: absolute; inset: 0; background-size: cover; background-position: center; opacity: 0; transition: opacity 1.2s ease-in-out; }
    .hero-slide.active { opacity: 1; }

    /* Feature cards */
    .feature-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .feature-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px rgba(0,0,0,0.10); }

    /* Spot cards */
    .spot-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .spot-card:hover { transform: translateY(-4px); box-shadow: 0 16px 32px rgba(0,0,0,0.12); }
    .spot-card:hover .spot-img { transform: scale(1.07); }
    .spot-img { transition: transform 0.5s ease; }

    /* Gradient overlay on hero */
    .hero-overlay { background: linear-gradient(to bottom, rgba(0,0,0,0.15) 0%, rgba(0,0,0,0.55) 70%, rgba(0,0,0,0.75) 100%); }

    /* Section wave divider */
    .wave-top { margin-top: -2px; }
</style>
@endpush

{{-- ═══════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════════ --}}
<section class="relative h-[100svh] min-h-[600px] overflow-hidden flex items-end pb-20 md:pb-28" id="hero">

    {{-- 背景輪播圖片 --}}
    <div class="hero-slide active" style="background-image:url('https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1490806843957-31f4c9a91c65?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1502602898657-3e91760cbb34?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1920&q=80')"></div>
    <div class="hero-slide" style="background-image:url('https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1920&q=80')"></div>

    {{-- 深色漸層遮罩 --}}
    <div class="hero-overlay absolute inset-0 z-10"></div>

    {{-- 輪播指示點 --}}
    <div class="absolute bottom-6 left-1/2 -translate-x-1/2 z-30 flex gap-2" id="hero-dots"></div>

    {{-- 內容 --}}
    <div class="relative z-20 w-full max-w-5xl mx-auto px-6 lg:px-8">

        <div class="mb-8" data-aos="fade-up" data-aos-duration="800">
            <span class="inline-block bg-white/20 backdrop-blur-sm text-white text-xs font-semibold tracking-widest uppercase px-4 py-1.5 rounded-full border border-white/30 mb-5">
                ✦ AI 智慧旅遊規劃平台
            </span>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight tracking-tight drop-shadow-lg">
                規劃你的下一趟<br>
                <span class="text-indigo-300">完美旅程</span>
            </h1>
            <p class="mt-5 text-white/80 text-lg md:text-xl font-medium max-w-xl leading-relaxed drop-shadow">
                用 AI 幫你生成行程、在地圖上規劃每一站，<br class="hidden md:block">然後分享給所有旅人。
            </p>
        </div>

        {{-- 搜尋 / CTA 按鈕組 --}}
        <div class="flex flex-col sm:flex-row gap-3" data-aos="fade-up" data-aos-duration="800" data-aos-delay="150">
            <a href="/map" class="inline-flex items-center justify-center gap-2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-base px-8 py-4 rounded-2xl shadow-lg transition-all duration-200 hover:scale-[1.03]">
                <i class="bi bi-map-fill text-lg"></i> 開始規劃行程
            </a>
            <a href="/feed" class="inline-flex items-center justify-center gap-2 bg-white/15 hover:bg-white/25 backdrop-blur-sm text-white font-semibold text-base px-8 py-4 rounded-2xl border border-white/30 transition-all duration-200 hover:scale-[1.03]">
                <i class="bi bi-compass text-lg"></i> 探索旅遊靈感
            </a>
        </div>

        {{-- 統計小數字 --}}
        <div class="mt-10 flex items-center gap-6 text-white/70 text-sm font-medium" data-aos="fade-up" data-aos-delay="300">
            <span class="flex items-center gap-1.5"><i class="bi bi-people-fill text-indigo-300"></i> 社群旅人分享</span>
            <span class="w-px h-4 bg-white/30"></span>
            <span class="flex items-center gap-1.5"><i class="bi bi-robot text-indigo-300"></i> Gemini AI 驅動</span>
            <span class="w-px h-4 bg-white/30"></span>
            <span class="flex items-center gap-1.5"><i class="bi bi-globe-asia-australia text-indigo-300"></i> 全球景點</span>
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     FEATURE CARDS SECTION
════════════════════════════════════════════════ --}}
<section class="py-20 bg-white" id="features">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">

        <div class="text-center mb-14" data-aos="fade-up">
            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">你想做什麼旅行？</h2>
            <p class="mt-3 text-slate-500 text-base md:text-lg">四大功能，為你打造最完整的旅遊體驗</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- 地圖規劃 --}}
            <a href="/map" class="feature-card group bg-gradient-to-br from-indigo-50 to-blue-50 border border-indigo-100 rounded-3xl p-7 flex flex-col gap-5 shadow-sm cursor-pointer" data-aos="fade-up" data-aos-delay="0">
                <div class="w-14 h-14 rounded-2xl bg-indigo-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-map-fill text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800 mb-2">地圖規劃</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">在互動式地圖上搜尋景點、拖拉排序，輕鬆安排每天行程。</p>
                </div>
                <span class="mt-auto text-indigo-600 text-sm font-bold flex items-center gap-1 group-hover:gap-2.5 transition-all">前往規劃 <i class="bi bi-arrow-right"></i></span>
            </a>

            {{-- AI 行程生成 --}}
            <a href="/map" class="feature-card group bg-gradient-to-br from-violet-50 to-purple-50 border border-violet-100 rounded-3xl p-7 flex flex-col gap-5 shadow-sm cursor-pointer" data-aos="fade-up" data-aos-delay="80">
                <div class="w-14 h-14 rounded-2xl bg-violet-600 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-robot text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800 mb-2">AI 行程生成</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">告訴 AI 你的喜好和天數，自動幫你規劃含交通、費用的完整行程。</p>
                </div>
                <span class="mt-auto text-violet-600 text-sm font-bold flex items-center gap-1 group-hover:gap-2.5 transition-all">體驗 AI <i class="bi bi-arrow-right"></i></span>
            </a>

            {{-- 社群動態 --}}
            <a href="/feed" class="feature-card group bg-gradient-to-br from-rose-50 to-pink-50 border border-rose-100 rounded-3xl p-7 flex flex-col gap-5 shadow-sm cursor-pointer" data-aos="fade-up" data-aos-delay="160">
                <div class="w-14 h-14 rounded-2xl bg-rose-500 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-compass-fill text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800 mb-2">靈感社群</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">瀏覽其他旅人分享的遊記和行程，找到你下一趟旅行的靈感。</p>
                </div>
                <span class="mt-auto text-rose-500 text-sm font-bold flex items-center gap-1 group-hover:gap-2.5 transition-all">看看大家 <i class="bi bi-arrow-right"></i></span>
            </a>

            {{-- 熱門排行榜 --}}
            <a href="/ranking" class="feature-card group bg-gradient-to-br from-amber-50 to-orange-50 border border-amber-100 rounded-3xl p-7 flex flex-col gap-5 shadow-sm cursor-pointer" data-aos="fade-up" data-aos-delay="240">
                <div class="w-14 h-14 rounded-2xl bg-amber-500 flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-trophy-fill text-white text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-extrabold text-slate-800 mb-2">熱門排行榜</h3>
                    <p class="text-slate-500 text-sm leading-relaxed">看看 TripFlow 社群最多人排入行程的熱門景點 Top 15。</p>
                </div>
                <span class="mt-auto text-amber-600 text-sm font-bold flex items-center gap-1 group-hover:gap-2.5 transition-all">查看榜單 <i class="bi bi-arrow-right"></i></span>
            </a>

        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     RECOMMENDED SPOTS SECTION
════════════════════════════════════════════════ --}}
<section class="py-20 bg-slate-50" id="featured-spots">
    <div class="max-w-6xl mx-auto px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4" data-aos="fade-up">
            <div>
                <span class="text-indigo-600 text-sm font-bold tracking-widest uppercase">社群熱門</span>
                <h2 class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight mt-2">大家都在去的地方</h2>
                <p class="mt-2 text-slate-500">根據 TripFlow 社群行程統計，最多旅人排入行程的景點</p>
            </div>
            <a href="/ranking" class="shrink-0 inline-flex items-center gap-2 text-sm font-bold text-indigo-600 hover:text-indigo-800 transition border border-indigo-200 hover:border-indigo-400 px-5 py-2.5 rounded-xl bg-white shadow-sm hover:shadow">
                查看完整榜單 <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        @if(count($featuredPlaces) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($featuredPlaces as $index => $place)
            <div class="spot-card bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 flex flex-col"
                 data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">

                {{-- 圖片區 --}}
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    @if(!empty($place['photo']))
                        <img src="{{ $place['photo'] }}" alt="{{ $place['name'] }}"
                             class="spot-img w-full h-full object-cover">
                    @else
                        {{-- Fallback: 依照 index 用不同 Unsplash 旅遊圖 --}}
                        @php
                            $fallbacks = [
                                'https://images.unsplash.com/photo-1528360983277-13d401cdc186?w=600&q=75',
                                'https://images.unsplash.com/photo-1566438480900-0609be27a4be?w=600&q=75',
                                'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?w=600&q=75',
                                'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=600&q=75',
                                'https://images.unsplash.com/photo-1469854523086-cc02fe5d8800?w=600&q=75',
                                'https://images.unsplash.com/photo-1488085061387-422e29b40080?w=600&q=75',
                            ];
                        @endphp
                        <img src="{{ $fallbacks[$index % 6] }}" alt="{{ $place['name'] }}"
                             class="spot-img w-full h-full object-cover opacity-80">
                    @endif

                    {{-- 排名徽章 --}}
                    <div class="absolute top-3 left-3">
                        @if($index === 0)
                            <span class="bg-amber-400 text-white text-xs font-black px-3 py-1 rounded-full shadow flex items-center gap-1"><i class="bi bi-trophy-fill"></i> #1</span>
                        @elseif($index === 1)
                            <span class="bg-slate-400 text-white text-xs font-black px-3 py-1 rounded-full shadow">#2</span>
                        @elseif($index === 2)
                            <span class="bg-orange-400 text-white text-xs font-black px-3 py-1 rounded-full shadow">#3</span>
                        @else
                            <span class="bg-slate-700/80 text-white text-xs font-bold px-3 py-1 rounded-full shadow">#{{ $index + 1 }}</span>
                        @endif
                    </div>

                    {{-- 評分 --}}
                    @if(!empty($place['rating']))
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-slate-800 text-xs font-bold px-2.5 py-1 rounded-full shadow flex items-center gap-1">
                        <i class="bi bi-star-fill text-amber-400 text-[10px]"></i> {{ $place['rating'] }}
                    </div>
                    @endif
                </div>

                {{-- 資訊區 --}}
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-extrabold text-slate-800 text-base mb-1.5 leading-snug">{{ $place['name'] }}</h3>

                    @if(!empty($place['types']) && is_array($place['types']))
                    <span class="inline-block text-[10px] font-semibold text-slate-500 uppercase tracking-widest bg-slate-100 px-2 py-0.5 rounded-md w-fit mb-3">
                        {{ str_replace('_', ' ', $place['types'][0] ?? '景點') }}
                    </span>
                    @endif

                    <div class="mt-auto flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-500 font-medium flex items-center gap-1">
                            <i class="bi bi-people-fill text-indigo-400"></i> {{ $place['count'] }} 人排入行程
                        </span>
                        <a href="/map" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">
                            加入行程 <i class="bi bi-plus-circle"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        {{-- 空狀態：還沒有行程資料時顯示靜態卡片 --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @php
                $demoSpots = [
                    ['name' => '東京晴空塔', 'tag' => 'tourist attraction', 'img' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?w=600&q=75'],
                    ['name' => '巴黎艾菲爾鐵塔', 'tag' => 'landmark', 'img' => 'https://images.unsplash.com/photo-1502602898657-3e91760cbb34?w=600&q=75'],
                    ['name' => '峇里島海神廟', 'tag' => 'temple', 'img' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?w=600&q=75'],
                    ['name' => '京都伏見稻荷', 'tag' => 'shrine', 'img' => 'https://images.unsplash.com/photo-1490806843957-31f4c9a91c65?w=600&q=75'],
                    ['name' => '希臘聖托里尼', 'tag' => 'island', 'img' => 'https://images.unsplash.com/photo-1570077188670-e3a8d69ac5ff?w=600&q=75'],
                    ['name' => '馬爾地夫水屋', 'tag' => 'resort', 'img' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=600&q=75'],
                ];
            @endphp
            @foreach($demoSpots as $i => $spot)
            <div class="spot-card bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 flex flex-col"
                 data-aos="fade-up" data-aos-delay="{{ $i * 80 }}">
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    <img src="{{ $spot['img'] }}" alt="{{ $spot['name'] }}" class="spot-img w-full h-full object-cover">
                    @if($i < 3)
                    <div class="absolute top-3 left-3">
                        <span class="{{ ['bg-amber-400','bg-slate-400','bg-orange-400'][$i] }} text-white text-xs font-black px-3 py-1 rounded-full shadow">#{{ $i+1 }}</span>
                    </div>
                    @endif
                    <div class="absolute top-3 right-3 bg-white/90 text-slate-800 text-xs font-bold px-2.5 py-1 rounded-full shadow flex items-center gap-1">
                        <i class="bi bi-star-fill text-amber-400 text-[10px]"></i> 4.{{ 9 - $i }}
                    </div>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-extrabold text-slate-800 text-base mb-1.5">{{ $spot['name'] }}</h3>
                    <span class="inline-block text-[10px] font-semibold text-slate-500 uppercase tracking-widest bg-slate-100 px-2 py-0.5 rounded-md w-fit mb-3">{{ $spot['tag'] }}</span>
                    <div class="mt-auto flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-xs text-slate-500 font-medium flex items-center gap-1"><i class="bi bi-people-fill text-indigo-400"></i> 熱門景點</span>
                        <a href="/map" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">加入行程 <i class="bi bi-plus-circle"></i></a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     CTA BANNER
════════════════════════════════════════════════ --}}
<section class="py-20 bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700" data-aos="fade-up">
    <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
        <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-4">
            準備好出發了嗎？
        </h2>
        <p class="text-indigo-200 text-lg mb-10 leading-relaxed">
            讓 AI 幫你規劃，讓地圖幫你導航，<br class="hidden md:block">讓社群幫你找靈感。
        </p>
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <a href="/map" class="inline-flex items-center gap-2 bg-white text-indigo-700 font-bold text-base px-10 py-4 rounded-2xl shadow-xl hover:bg-indigo-50 transition-all duration-200 hover:scale-[1.03]">
                <i class="bi bi-map-fill"></i> 立即開始規劃
            </a>
            @guest
            <a href="{{ route('register') }}" class="inline-flex items-center gap-2 bg-white/15 hover:bg-white/25 text-white font-semibold text-base px-10 py-4 rounded-2xl border border-white/30 transition-all duration-200 hover:scale-[1.03]">
                <i class="bi bi-person-plus"></i> 免費建立帳號
            </a>
            @endguest
        </div>
    </div>
</section>

{{-- ═══════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════════ --}}
<footer class="bg-slate-900 text-slate-400 py-10">
    <div class="max-w-6xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <span class="text-white font-extrabold text-lg tracking-tight">TripFlow<span class="text-indigo-400">.</span></span>
            <span class="text-slate-600">|</span>
            <span class="text-sm">智慧旅遊規劃平台</span>
        </div>
        <div class="flex items-center gap-6 text-sm">
            <a href="/map" class="hover:text-white transition">探索地圖</a>
            <a href="/feed" class="hover:text-white transition">靈感社群</a>
            <a href="/ranking" class="hover:text-white transition">熱門榜單</a>
            @auth
            <a href="{{ route('dashboard') }}" class="hover:text-white transition">我的行程</a>
            @endauth
        </div>
        <p class="text-xs text-slate-600">© {{ date('Y') }} TripFlow. Powered by Gemini AI & Google Maps.</p>
    </div>
</footer>

{{-- AOS + Hero 輪播 JS --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
    AOS.init({ once: true, offset: 60 });

    // Hero 輪播
    const slides = document.querySelectorAll('.hero-slide');
    const dotsContainer = document.getElementById('hero-dots');
    let current = 0;

    slides.forEach((_, i) => {
        const dot = document.createElement('button');
        dot.className = 'w-2 h-2 rounded-full transition-all duration-300 ' + (i === 0 ? 'bg-white w-5' : 'bg-white/40');
        dot.addEventListener('click', () => goTo(i));
        dotsContainer.appendChild(dot);
    });

    function goTo(index) {
        slides[current].classList.remove('active');
        dotsContainer.children[current].className = 'w-2 h-2 rounded-full transition-all duration-300 bg-white/40';
        current = index;
        slides[current].classList.add('active');
        dotsContainer.children[current].className = 'w-5 h-2 rounded-full transition-all duration-300 bg-white';
    }

    setInterval(() => goTo((current + 1) % slides.length), 5000);
</script>
@endpush

</x-app-layout>
