<x-app-layout>

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" />
<style>
    .rank-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .rank-card:hover { transform: translateY(-5px); box-shadow: 0 20px 40px rgba(0,0,0,0.10); }
    .rank-card:hover .rank-img { transform: scale(1.06); }
    .rank-img { transition: transform 0.5s ease; }
</style>
@endpush

<div class="min-h-screen bg-slate-50">

    {{-- Page Header --}}
    <div class="bg-gradient-to-br from-slate-900 via-slate-800 to-indigo-900 pt-14 pb-20 px-6 text-center relative overflow-hidden">
        <div class="absolute inset-0 opacity-10" style="background-image:url('https://images.unsplash.com/photo-1488085061387-422e29b40080?auto=format&fit=crop&w=1920&q=60'); background-size:cover; background-position:center;"></div>
        <div class="relative z-10" data-aos="fade-up">
            <span class="inline-flex items-center gap-2 bg-amber-400/20 text-amber-300 text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full border border-amber-400/30 mb-5">
                <i class="bi bi-trophy-fill"></i> 社群熱門榜單
            </span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight mb-3">全球熱門景點 Top 15</h1>
            <p class="text-slate-400 text-base md:text-lg max-w-xl mx-auto">TripFlow 社群旅人最常排入行程的景點，激發你的旅行靈感！</p>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 lg:px-8 -mt-10 pb-24">

        @forelse ($topPlaces as $index => $place)

        {{-- TOP 3：大卡片 --}}
        @if($index < 3)

        @if($index === 0)
        <div class="mb-8" data-aos="fade-up">
            <div class="rank-card relative bg-white rounded-3xl overflow-hidden shadow-md border border-slate-100 flex flex-col md:flex-row">
                {{-- 圖片 --}}
                <div class="relative md:w-2/5 h-64 md:h-auto overflow-hidden bg-slate-100 shrink-0">
                    @if(!empty($place['photo']))
                        <img src="{{ $place['photo'] }}" alt="{{ $place['name'] }}" class="rank-img w-full h-full object-cover">
                    @else
                        <img src="https://images.unsplash.com/photo-1488085061387-422e29b40080?w=800&q=75" alt="{{ $place['name'] }}" class="rank-img w-full h-full object-cover opacity-70">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-r from-transparent to-white/10 md:bg-gradient-to-l"></div>
                    <div class="absolute top-4 left-4">
                        <span class="bg-amber-400 text-white text-sm font-black px-4 py-1.5 rounded-full shadow-lg flex items-center gap-1.5">
                            <i class="bi bi-trophy-fill"></i> #1 最熱門
                        </span>
                    </div>
                </div>
                {{-- 資訊 --}}
                <div class="flex-1 p-8 flex flex-col justify-center">
                    <div class="flex flex-wrap gap-2 mb-4">
                        @if(!empty($place['types']))
                        <span class="bg-slate-100 text-slate-600 text-[11px] font-bold uppercase tracking-widest px-3 py-1 rounded-full border border-slate-200">
                            {{ str_replace('_', ' ', $place['types'][0] ?? '景點') }}
                        </span>
                        @endif
                        @if(!empty($place['rating']))
                        <span class="bg-amber-50 text-amber-700 text-[11px] font-bold px-3 py-1 rounded-full border border-amber-200 flex items-center gap-1">
                            <i class="bi bi-star-fill text-amber-400"></i> {{ $place['rating'] }}
                        </span>
                        @endif
                    </div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-slate-800 mb-3 leading-tight">{{ $place['name'] }}</h2>
                    <p class="text-slate-500 text-sm mb-6 flex items-center gap-2">
                        <i class="bi bi-people-fill text-indigo-400"></i>
                        已有 <strong class="text-slate-800 text-base">{{ $place['count'] }}</strong> 位旅人將此景點排入行程
                    </p>
                    <a href="/map" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-3 rounded-xl transition w-fit shadow-sm">
                        <i class="bi bi-map-fill"></i> 加入我的行程
                    </a>
                </div>
            </div>
        </div>
        @endif

        @if($index === 1 || $index === 2)
        @if($index === 1)<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">@endif
            <div class="rank-card bg-white rounded-3xl overflow-hidden shadow-sm border border-slate-100 flex flex-col" data-aos="fade-up" data-aos-delay="{{ ($index - 1) * 100 }}">
                <div class="relative h-52 overflow-hidden bg-slate-100">
                    @if(!empty($place['photo']))
                        <img src="{{ $place['photo'] }}" alt="{{ $place['name'] }}" class="rank-img w-full h-full object-cover">
                    @else
                        <img src="https://images.unsplash.com/photo-{{ $index === 1 ? '1476514525535-07fb3b4ae5f1' : '1500534314209-a25ddb2bd429' }}?w=600&q=75" alt="{{ $place['name'] }}" class="rank-img w-full h-full object-cover opacity-70">
                    @endif
                    <div class="absolute top-3 left-3">
                        <span class="{{ $index === 1 ? 'bg-slate-400' : 'bg-orange-400' }} text-white text-xs font-black px-3 py-1 rounded-full shadow">#{{ $index + 1 }}</span>
                    </div>
                    @if(!empty($place['rating']))
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm text-slate-800 text-xs font-bold px-2.5 py-1 rounded-full shadow flex items-center gap-1">
                        <i class="bi bi-star-fill text-amber-400 text-[10px]"></i> {{ $place['rating'] }}
                    </div>
                    @endif
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h3 class="font-extrabold text-slate-800 text-lg mb-2 leading-tight">{{ $place['name'] }}</h3>
                    @if(!empty($place['types']))
                    <span class="inline-block text-[10px] font-semibold text-slate-500 uppercase tracking-widest bg-slate-100 px-2 py-0.5 rounded-md w-fit mb-3">{{ str_replace('_', ' ', $place['types'][0] ?? '景點') }}</span>
                    @endif
                    <div class="mt-auto flex items-center justify-between pt-3 border-t border-slate-100">
                        <span class="text-sm font-bold text-slate-700 flex items-center gap-1.5"><i class="bi bi-people-fill text-indigo-400"></i> {{ $place['count'] }} 人排入</span>
                        <a href="/map" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 transition">加入行程 <i class="bi bi-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        @if($index === 2)</div>@endif
        @endif

        @else

        {{-- #4 以後：清單卡片 --}}
        @if($index === 3)
        <div class="mb-4" data-aos="fade-up">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2 mb-5">
                <span class="flex-1 h-px bg-slate-200"></span> 其他熱門景點 <span class="flex-1 h-px bg-slate-200"></span>
            </h3>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @endif

            <div class="rank-card bg-white rounded-2xl overflow-hidden shadow-sm border border-slate-100 flex flex-col"
                 data-aos="fade-up" data-aos-delay="{{ (($index - 3) % 3) * 80 }}">
                <div class="relative h-40 overflow-hidden bg-slate-100">
                    @if(!empty($place['photo']))
                        <img src="{{ $place['photo'] }}" alt="{{ $place['name'] }}" class="rank-img w-full h-full object-cover">
                    @else
                        @php
                            $fb = ['1528360983277-13d401cdc186','1566438480900-0609be27a4be','1476514525535-07fb3b4ae5f1','1500534314209-a25ddb2bd429','1469854523086-cc02fe5d8800','1488085061387-422e29b40080','1507525428034-b723cf961d3e','1537996194471-e657df975ab4','1540959733332-eab4deabeeaf','1490806843957-31f4c9a91c65','1570077188670-e3a8d69ac5ff','1502602898657-3e91760cbb34'];
                        @endphp
                        <img src="https://images.unsplash.com/photo-{{ $fb[$index % count($fb)] }}?w=400&q=70" alt="{{ $place['name'] }}" class="rank-img w-full h-full object-cover opacity-70">
                    @endif
                    <div class="absolute top-2.5 left-2.5">
                        <span class="bg-slate-700/80 text-white text-[11px] font-bold px-2.5 py-0.5 rounded-full">#{{ $index + 1 }}</span>
                    </div>
                    @if(!empty($place['rating']))
                    <div class="absolute top-2.5 right-2.5 bg-white/90 text-slate-800 text-[11px] font-bold px-2 py-0.5 rounded-full flex items-center gap-0.5">
                        <i class="bi bi-star-fill text-amber-400 text-[9px]"></i> {{ $place['rating'] }}
                    </div>
                    @endif
                </div>
                <div class="p-4 flex flex-col flex-1">
                    <h3 class="font-extrabold text-slate-800 text-sm mb-1 leading-snug">{{ $place['name'] }}</h3>
                    @if(!empty($place['types']))
                    <span class="text-[9px] font-semibold text-slate-400 uppercase tracking-widest mb-2">{{ str_replace('_', ' ', $place['types'][0] ?? '景點') }}</span>
                    @endif
                    <div class="mt-auto flex items-center justify-between pt-2.5 border-t border-slate-100">
                        <span class="text-xs text-slate-500 flex items-center gap-1"><i class="bi bi-people-fill text-indigo-300 text-[10px]"></i> {{ $place['count'] }} 人排入</span>
                        <a href="/map" class="text-[11px] font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-0.5 transition"><i class="bi bi-plus-circle"></i></a>
                    </div>
                </div>
            </div>

        @if($index === count($topPlaces) - 1)</div>@endif

        @endif

        @empty
        <div class="text-center py-24 bg-white rounded-3xl shadow-sm border border-slate-100 mt-4" data-aos="fade-up">
            <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-5 border border-slate-100 shadow-sm">
                <i class="bi bi-trophy text-3xl text-slate-300"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-700 mb-2">排行榜目前還在統計中</h3>
            <p class="text-slate-400 text-sm mb-8">快去地圖上規劃並儲存你的行程，成為第一個上榜的旅人！</p>
            <a href="/map" class="inline-flex items-center gap-2 bg-slate-800 text-white px-8 py-3 rounded-xl text-sm font-semibold shadow-sm hover:bg-slate-700 transition">
                <i class="bi bi-map-fill"></i> 前往探索地圖
            </a>
        </div>
        @endforelse

    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ once: true, offset: 40 });</script>
@endpush

</x-app-layout>
