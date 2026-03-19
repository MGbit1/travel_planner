<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('控制台 / 我的行程') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="bg-green-50 text-green-700 px-4 py-3 rounded-xl mb-6 shadow-sm font-bold border border-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl p-6 border border-slate-100">
                
                <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                    <h3 class="text-xl font-extrabold flex items-center gap-2 text-slate-800">
                        <i class="bi bi-folder-fill text-indigo-600"></i> 我儲存的行程
                    </h3>
                    <a href="/?new=1" class="bg-indigo-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md hover:bg-indigo-700 hover:-translate-y-0.5 transition-all flex items-center gap-2">
                        <i class="bi bi-plus-lg text-lg leading-none"></i> 創建新行程
                    </a>
                </div>
                @if(auth()->user()->trips && auth()->user()->trips->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach(auth()->user()->trips as $trip)
                            <div class="border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-lg transition-all duration-300 bg-slate-50 flex flex-col group">
                                <h4 class="font-extrabold text-lg text-slate-800 mb-1 truncate group-hover:text-blue-600 transition">{{ $trip->title }}</h4>
                                <p class="text-xs text-slate-500 mb-5 font-bold"><i class="bi bi-clock"></i> 建立於：{{ $trip->created_at->format('Y-m-d') }}</p>
                                
                                <div class="mt-auto flex gap-2">
                                    <a href="/?trip_id={{ $trip->id }}" class="flex-1 bg-blue-100 text-blue-700 text-center py-2.5 rounded-xl text-sm font-bold hover:bg-blue-200 transition">
                                        <i class="bi bi-map"></i> 載入編輯
                                    </a>
                                    
                                    <button onclick="openShareModal({{ $trip->id }}, '{{ addslashes($trip->title) }}', {{ is_array($trip->itinerary_data) ? count($trip->itinerary_data) : 1 }})" class="flex-1 bg-indigo-600 text-white text-center py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-sm">
                                        <i class="bi bi-send-fill"></i> 分享發佈
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-16 bg-slate-50 rounded-2xl border-2 border-dashed border-slate-200">
                        <i class="bi bi-compass text-5xl text-slate-300 mb-3 block"></i>
                        <p class="text-slate-600 text-lg font-bold">目前還沒有儲存任何行程喔！</p>
                        <a href="/" class="inline-block mt-4 bg-blue-600 text-white px-5 py-2 rounded-xl font-bold hover:bg-blue-700 transition shadow-md">去地圖規劃</a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="shareModal" class="fixed inset-0 bg-black/50 hidden flex items-center justify-center z-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-3xl shadow-2xl w-full max-w-md p-6 transform scale-95 opacity-0 transition-all duration-300" id="shareModalContent">
            <div class="flex justify-between items-center mb-5">
                <h3 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-stars text-indigo-600"></i> 發佈到社群動態
                </h3>
                <button onclick="closeShareModal()" class="text-slate-400 hover:text-red-500 text-2xl font-bold leading-none">&times;</button>
            </div>
            
            <form action="{{ route('feed.store') }}" method="POST">
                @csrf
                <input type="hidden" name="trip_id" id="modal_trip_id">
                <input type="hidden" name="days_count" id="modal_days_count">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">貼文標題</label>
                        <input type="text" name="title" id="modal_title" required class="w-full border border-slate-300 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-indigo-500 outline-none text-sm font-bold text-slate-800">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1">旅遊心得 / 介紹</label>
                        <textarea name="content" rows="4" placeholder="跟大家分享這趟旅程的亮點、美食或是好玩的體驗吧！" class="w-full border border-slate-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-indigo-500 outline-none resize-none text-sm"></textarea>
                    </div>
                    
                    <input type="hidden" name="image_url" value="">
                    
                    <div class="pt-3 flex gap-3">
                        <button type="button" onclick="closeShareModal()" class="flex-1 bg-slate-100 text-slate-600 py-3 rounded-xl font-bold hover:bg-slate-200 transition">取消</button>
                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-xl font-bold hover:bg-indigo-700 transition shadow-md">確認發佈 🚀</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openShareModal(tripId, title, days) {
            document.getElementById('modal_trip_id').value = tripId;
            document.getElementById('modal_title').value = title;
            document.getElementById('modal_days_count').value = days || 1;
            
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
    </script>
</x-app-layout>