<x-app-layout>
    <x-slot name="header">
        <h2 class="font-extrabold text-xl text-indigo-800 leading-tight flex items-center gap-2">
            <span>🏠</span> {{ __('會員控制台') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-3xl border border-slate-100 transform transition hover:-translate-y-1">
                <div class="p-8 md:p-12 text-slate-800 font-bold flex flex-col md:flex-row items-center gap-6">
                    <div class="text-6xl bg-blue-50 p-6 rounded-full border-4 border-white shadow-inner">🎉</div> 
                    <div>
                        <h3 class="text-2xl font-black text-indigo-700 mb-2">歡迎回來，{{ Auth::user()->name }}！</h3>
                        <p class="text-base text-slate-500 font-medium leading-relaxed">
                            您已成功登入專屬帳號。在這裡可以管理您所有的完美旅程。
                        </p>
                    </div>
                </div>
                <div class="bg-gradient-to-r from-indigo-50 to-blue-50 px-8 py-5 border-t border-indigo-100 flex justify-end">
                    <a href="/" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm px-6 py-2.5 rounded-xl shadow-md transition flex items-center gap-2 transform hover:scale-105">
                        <span>🗺️</span> 前往地圖建立新行程
                    </a>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-extrabold text-slate-800 mb-5 flex items-center gap-2 ml-2">
                    <span>🧳</span> 我的專屬行程庫
                </h3>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 font-bold rounded-xl shadow-sm animate-in fade-in">
                        ✅ {{ session('success') }}
                    </div>
                @endif

                @if($trips->isEmpty())
                    <div class="bg-white rounded-3xl border-2 border-dashed border-slate-200 p-12 text-center shadow-sm">
                        <div class="text-5xl mb-4 opacity-50">🏝️</div>
                        <p class="text-slate-500 font-bold mb-5 text-lg">您的行程庫目前空空如也喔！</p>
                        <a href="/" class="bg-indigo-50 text-indigo-600 hover:bg-indigo-100 border border-indigo-100 font-bold px-6 py-3 rounded-xl transition inline-flex items-center gap-2 shadow-sm">
                            馬上開始規劃第一趟旅程 🚀
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($trips as $trip)
                            <div class="bg-white rounded-2xl border border-slate-100 shadow-md hover:shadow-xl transition-all transform hover:-translate-y-1.5 overflow-hidden flex flex-col group">
                                <div class="p-6 flex-1 relative">
                                    <div class="absolute top-0 right-0 w-20 h-20 bg-gradient-to-br from-indigo-100 to-white rounded-bl-full -z-10 opacity-50 group-hover:scale-110 transition-transform"></div>
                                    
                                    <div class="flex justify-between items-start mb-4 gap-2">
                                        <h4 class="font-black text-lg text-slate-800 leading-snug line-clamp-2">{{ $trip->title }}</h4>
                                    </div>

                                    @php
                                        // 算一下這個行程有幾天、幾個景點
                                        $daysCount = count($trip->itinerary_data ?? []);
                                        $placesCount = 0;
                                        foreach($trip->itinerary_data ?? [] as $dayPoints) {
                                            $placesCount += count($dayPoints);
                                        }
                                    @endphp

                                    <div class="inline-flex gap-3 text-[13px] text-slate-600 font-bold bg-slate-50 px-3 py-2 rounded-lg border border-slate-100">
                                        <span class="flex items-center gap-1">📅 {{ $daysCount }} 天</span>
                                        <span class="text-slate-300">|</span>
                                        <span class="flex items-center gap-1 text-indigo-600">📍 {{ $placesCount }} 個景點</span>
                                    </div>
                                    
                                    <p class="text-[11px] text-slate-400 mt-4 font-semibold flex items-center gap-1">
                                        🕒 建立於 {{ $trip->created_at->format('Y-m-d H:i') }}
                                    </p>
                                </div>
                                
                                <div class="bg-slate-50 px-5 py-4 border-t border-slate-100 flex justify-between items-center gap-3">
                                    <a href="/?trip_id={{ $trip->id }}" class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-center text-sm font-bold py-2.5 rounded-xl transition shadow-md flex justify-center items-center gap-1.5">
                                        <span>📂</span> 載入行程
                                    </a>
                                    
                                    <form action="{{ route('trips.destroy', $trip->id) }}" method="POST" class="inline m-0" onsubmit="return confirm('🚨 確定要永久刪除「{{ $trip->title }}」嗎？');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-white border border-red-200 text-red-500 hover:bg-red-50 hover:border-red-300 text-sm font-bold p-2.5 rounded-xl transition shadow-sm" title="刪除行程">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>