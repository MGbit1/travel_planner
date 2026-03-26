<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-slate-800 leading-tight flex items-center gap-2">
            <i class="bi bi-trophy text-indigo-500"></i> {{ __('熱門榜單') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 mb-20">
            
            <div class="text-center mb-12">
                <h1 class="text-3xl font-extrabold text-slate-800 tracking-tight mb-3">全球熱門景點 Top 15</h1>
                <p class="text-slate-500 font-medium">看看 TripFlow 社群中的旅人們都在把哪些景點排進旅程，激發你的旅行靈感！</p>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                @forelse ($topPlaces as $index => $place)
                    <div class="flex items-center gap-4 md:gap-6 p-4 md:p-6 border-b border-slate-100 hover:bg-slate-50 transition duration-300 group last:border-b-0">
                        
                        <div class="w-12 shrink-0 flex justify-center items-center">
                            @if($index === 0)
                                <div class="w-10 h-10 bg-amber-50 rounded-full flex items-center justify-center text-amber-600 text-lg font-black border border-amber-200 shadow-sm relative">
                                    1 <i class="bi bi-award-fill absolute -top-2 -right-1 text-amber-400 text-sm"></i>
                                </div>
                            @elseif($index === 1)
                                <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center text-slate-500 text-lg font-black border border-slate-200 shadow-sm relative">
                                    2 <i class="bi bi-award-fill absolute -top-2 -right-1 text-slate-400 text-sm"></i>
                                </div>
                            @elseif($index === 2)
                                <div class="w-10 h-10 bg-orange-50 rounded-full flex items-center justify-center text-orange-600 text-lg font-black border border-orange-200 shadow-sm relative">
                                    3 <i class="bi bi-award-fill absolute -top-2 -right-1 text-orange-400 text-sm"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 bg-slate-50 rounded-full flex items-center justify-center text-slate-400 text-base font-bold border border-slate-100">
                                    {{ $index + 1 }}
                                </div>
                            @endif
                        </div>

                        <div class="w-20 h-20 md:w-28 md:h-28 shrink-0 rounded-2xl overflow-hidden bg-slate-50 border border-slate-100 relative shadow-sm">
                            @if(!empty($place['photo']))
                                <img src="{{ $place['photo'] }}" alt="{{ $place['name'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            @else
                                <div class="absolute inset-0 flex items-center justify-center text-slate-300">
                                    <i class="bi bi-image text-3xl opacity-40"></i>
                                </div>
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h2 class="text-lg md:text-xl font-extrabold text-slate-800 mb-2 truncate group-hover:text-indigo-600 transition-colors">{{ $place['name'] }}</h2>
                            <div class="flex flex-wrap items-center gap-3 text-sm">
                                @if(!empty($place['types']) && is_array($place['types']))
                                    <span class="bg-slate-100 text-slate-600 px-2.5 py-1 rounded-md font-semibold border border-slate-200 text-[10px] uppercase tracking-widest">
                                        {{ str_replace('_', ' ', $place['types'][0] ?? '景點') }}
                                    </span>
                                @endif
                                @if(!empty($place['rating']))
                                    <span class="flex items-center gap-1 font-bold text-slate-700 text-[12px]">
                                        <i class="bi bi-star-fill text-amber-400"></i> {{ $place['rating'] }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="text-right shrink-0 flex flex-col items-end">
                            <div class="text-2xl md:text-3xl font-black text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors">{{ $place['count'] }}</div>
                            <div class="text-[10px] md:text-[11px] font-semibold text-slate-400 uppercase tracking-widest">排入行程</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-slate-50/50">
                        <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-sm">
                            <i class="bi bi-trophy text-2xl text-slate-300"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-700 mb-1.5">排行榜目前還在統計中</h3>
                        <p class="text-slate-400 text-[13px] font-medium mb-6">快去地圖上規劃並儲存你的行程，成為第一個上榜的旅人吧！</p>
                        <a href="/" class="inline-block bg-slate-800 text-white px-6 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:bg-slate-700 transition">
                            前往探索地圖
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>