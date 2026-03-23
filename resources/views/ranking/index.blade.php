<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>熱門景點排行榜 - AI 旅程大師</title>
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
                <a href="{{ route('feed.index') }}" class="text-blue-200 hover:text-white transition font-bold">🌍 社群動態</a>
                <a href="/ranking" class="font-bold border-b-2 border-white pb-1">🏆 景點排行</a>
                <a href="/" class="text-blue-200 hover:text-white transition font-bold">在地圖規劃</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="bg-white text-indigo-600 px-3 py-1.5 rounded-lg text-sm font-black shadow-sm hover:bg-blue-50 transition">我的控制台</a>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-bold text-blue-100 hover:text-white transition">登入/註冊</a>
                @endauth
            </div>
        </div>
    </nav>

    <main class="max-w-4xl mx-auto p-6 mt-8 mb-20">
        
        <div class="text-center mb-12">
            <h1 class="text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-amber-500 to-orange-600 mb-4 inline-flex items-center gap-3">
                <i class="bi bi-trophy-fill text-amber-500"></i> 全網最熱門 Top 15
            </h1>
            <p class="text-slate-500 font-bold text-lg">看看大家都在把哪些景點排進旅程裡，激發你的旅行靈感！</p>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-200 overflow-hidden">
            @forelse ($topPlaces as $index => $place)
                <div class="flex items-center gap-4 md:gap-6 p-4 md:p-6 border-b border-slate-100 hover:bg-slate-50 transition duration-300 group">
                    
                    <div class="w-12 md:w-16 shrink-0 flex justify-center items-center">
                        @if($index === 0)
                            <div class="w-12 h-12 bg-gradient-to-br from-yellow-300 to-amber-500 rounded-full flex items-center justify-center text-white text-xl font-black shadow-lg shadow-amber-200 animate-bounce">🥇</div>
                        @elseif($index === 1)
                            <div class="w-10 h-10 bg-gradient-to-br from-slate-300 to-slate-400 rounded-full flex items-center justify-center text-white text-lg font-black shadow-md">🥈</div>
                        @elseif($index === 2)
                            <div class="w-10 h-10 bg-gradient-to-br from-orange-300 to-orange-500 rounded-full flex items-center justify-center text-white text-lg font-black shadow-md">🥉</div>
                        @else
                            <div class="text-2xl font-black text-slate-300">{{ $index + 1 }}</div>
                        @endif
                    </div>

                    <div class="w-20 h-20 md:w-28 md:h-28 shrink-0 rounded-2xl overflow-hidden bg-slate-100 shadow-sm border border-slate-200">
                        @if(!empty($place['photo']))
                            <img src="{{ $place['photo'] }}" alt="{{ $place['name'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="bi bi-camera-fill text-3xl opacity-50"></i>
                            </div>
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl md:text-2xl font-extrabold text-slate-800 mb-2 truncate group-hover:text-blue-600 transition">{{ $place['name'] }}</h2>
                        <div class="flex flex-wrap items-center gap-3 text-sm">
                            @if(!empty($place['types']) && is_array($place['types']))
                                <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-lg font-bold border border-blue-100 text-[11px] uppercase tracking-wider">
                                    {{ str_replace('_', ' ', $place['types'][0] ?? '景點') }}
                                </span>
                            @endif
                            @if(!empty($place['rating']))
                                <span class="flex items-center gap-1 font-bold text-amber-500">
                                    <i class="bi bi-star-fill"></i> {{ $place['rating'] }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right shrink-0">
                        <div class="text-3xl md:text-4xl font-black text-indigo-600 mb-1">{{ $place['count'] }}</div>
                        <div class="text-[11px] md:text-xs font-bold text-slate-400 uppercase tracking-widest">次排入行程</div>
                    </div>
                </div>
            @empty
                <div class="text-center py-20">
                    <i class="bi bi-inboxes text-6xl text-slate-300 mb-4 block"></i>
                    <h3 class="text-xl font-bold text-slate-700 mb-2">排行榜目前還在統計中...</h3>
                    <p class="text-slate-500">快去地圖上規劃並儲存你的行程，成為第一個上榜的大師吧！</p>
                    <a href="/" class="inline-block mt-6 bg-blue-600 text-white px-6 py-2.5 rounded-xl font-bold shadow-md hover:bg-blue-700 transition">去規劃行程</a>
                </div>
            @endforelse
        </div>
    </main>

</body>
</html>