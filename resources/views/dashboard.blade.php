<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-lg text-slate-800 leading-tight flex items-center gap-2">
            <i class="bi bi-grid-1x2-fill text-indigo-500"></i> {{ __('我的行程管理') }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl mb-6 text-sm font-medium border border-emerald-200 flex items-center gap-2 shadow-sm">
                    <i class="bi bi-check-circle-fill"></i> {{ session('success') }}
                </div>
            @endif

            {{-- 分頁標籤 --}}
            <div x-data="{ tab: 'trips' }" class="bg-white overflow-hidden sm:rounded-2xl border border-slate-200 shadow-[0_2px_10px_rgba(0,0,0,0.02)]">

                <div class="flex border-b border-slate-100 px-6 md:px-8 pt-6 gap-1">
                    <button @click="tab = 'trips'"
                        :class="tab === 'trips' ? 'border-indigo-600 text-indigo-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2.5 text-sm font-semibold border-b-2 transition flex items-center gap-2">
                        <i class="bi bi-map-fill"></i> 我的行程
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $trips->count() }}</span>
                    </button>
                    <button @click="tab = 'wishlist'"
                        :class="tab === 'wishlist' ? 'border-rose-500 text-rose-600 font-bold' : 'border-transparent text-slate-500 hover:text-slate-700'"
                        class="px-4 py-2.5 text-sm font-semibold border-b-2 transition flex items-center gap-2">
                        <i class="bi bi-bookmark-heart-fill"></i> 我的收藏
                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-1.5 py-0.5 rounded-full">{{ $wishlists->count() }}</span>
                    </button>
                </div>

                {{-- ── 行程分頁 ── --}}
                <div x-show="tab === 'trips'" class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 tracking-tight">所有旅程</h3>
                            <p class="text-sm text-slate-500 mt-1 font-medium">管理您儲存的旅遊草稿與計畫</p>
                        </div>
                        <a href="/map?new=1" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:bg-slate-700 transition flex items-center gap-2">
                            <i class="bi bi-plus-lg"></i> 建立新行程
                        </a>
                    </div>

                    @if(isset($trips) && $trips->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($trips as $trip)
                                <div class="border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 bg-white flex flex-col group relative">
                                    <div class="flex justify-between items-start mb-3">
                                        <h4 class="font-bold text-lg text-slate-800 truncate pr-4 group-hover:text-indigo-600 transition-colors">{{ $trip->title }}</h4>
                                        <div class="flex items-center gap-1.5">
                                            <form action="{{ route('trips.destroy', $trip->id) }}" method="POST"
                                                  data-title="{{ $trip->title }}"
                                                  onsubmit="return confirm('確定要刪除「' + this.dataset.title + '」這個行程嗎？此動作無法復原！')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-slate-300 hover:text-red-500 p-1.5 rounded-lg hover:bg-red-50 transition" title="刪除行程">
                                                    <i class="bi bi-trash3 text-[13px]"></i>
                                                </button>
                                            </form>
                                            <div class="bg-slate-50 text-slate-400 p-1.5 rounded-lg flex-shrink-0 border border-slate-100">
                                                <i class="bi bi-map"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-xs text-slate-500 mb-6 font-medium flex items-center gap-4">
                                        <span class="flex items-center gap-1.5"><i class="bi bi-calendar3"></i> 建立於 {{ $trip->created_at->format('Y-m-d') }}</span>
                                    </div>
                                    <div class="mt-auto flex gap-2.5">
                                        <a href="/map?trip_id={{ $trip->id }}" class="flex-1 bg-white border border-slate-200 text-slate-700 text-center py-2 rounded-xl text-[13px] font-semibold hover:bg-slate-50 hover:border-slate-300 transition">
                                            載入編輯
                                        </a>
                                        <button
                                            data-trip-id="{{ $trip->id }}"
                                            data-trip-title="{{ $trip->title }}"
                                            data-days="{{ is_array($trip->itinerary_data) ? count($trip->itinerary_data) : 1 }}"
                                            onclick="openShareModal(this.dataset.tripId, this.dataset.tripTitle, this.dataset.days)"
                                            class="flex-1 bg-slate-800 text-white text-center py-2 rounded-xl text-[13px] font-semibold hover:bg-slate-700 transition shadow-sm">
                                            分享至社群
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-20 bg-slate-50/50 rounded-2xl border border-dashed border-slate-300">
                            <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                                <i class="bi bi-folder-x text-2xl text-slate-300"></i>
                            </div>
                            <p class="text-slate-700 text-sm font-bold">目前還沒有儲存任何行程</p>
                            <p class="text-slate-400 text-xs mt-1.5 font-medium">開始規劃你的下一趟完美旅程吧！</p>
                            <a href="/map?new=1" class="inline-block mt-6 bg-white border border-slate-200 text-slate-700 px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
                                <i class="bi bi-compass mr-1"></i> 前往探索地圖
                            </a>
                        </div>
                    @endif
                </div>

                {{-- ── 收藏分頁 ── --}}
                <div x-show="tab === 'wishlist'" class="p-6 md:p-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800 tracking-tight">想去清單</h3>
                            <p class="text-sm text-slate-500 mt-1 font-medium">規劃行程時可快速加入地圖</p>
                        </div>
                    </div>

                    @if(isset($wishlists) && $wishlists->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
                            @foreach($wishlists as $item)
                                <div class="group bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md hover:-translate-y-1 transition-all duration-300 flex flex-col">
                                    {{-- 景點圖片 --}}
                                    <div class="relative h-36 bg-slate-100 overflow-hidden shrink-0">
                                        @if($item->image_url)
                                            <img src="{{ $item->image_url }}" alt="{{ $item->place_name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-slate-100 to-rose-50">
                                                <i class="bi bi-geo-alt-fill text-3xl text-rose-200"></i>
                                            </div>
                                        @endif
                                        @if($item->rating)
                                            <div class="absolute bottom-2 left-2 bg-white/95 backdrop-blur-sm text-amber-500 text-[11px] font-bold px-2 py-0.5 rounded-lg shadow-sm flex items-center gap-1 border border-amber-100">
                                                <i class="bi bi-star-fill text-[9px]"></i> {{ number_format($item->rating, 1) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="p-4 flex flex-col flex-1">
                                        <h4 class="font-bold text-slate-800 text-[14px] mb-1 group-hover:text-rose-600 transition-colors">{{ $item->place_name }}</h4>
                                        @if($item->address)
                                            <p class="text-[11px] text-slate-400 truncate flex items-center gap-1 mb-3">
                                                <i class="bi bi-geo-alt shrink-0"></i> {{ $item->address }}
                                            </p>
                                        @endif

                                        <div class="mt-auto flex gap-2">
                                            <a href="/map?search={{ urlencode($item->place_name) }}"
                                               class="flex-1 bg-slate-800 hover:bg-slate-700 text-white text-center py-2 rounded-xl text-[12px] font-bold transition shadow-sm flex items-center justify-center gap-1.5">
                                                <i class="bi bi-plus-circle"></i> 加入今日行程
                                            </a>
                                            <form action="{{ route('wishlist.destroy', $item->id) }}" method="POST"
                                                  onsubmit="return confirm('確定移除「{{ $item->place_name }}」？')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="px-3 py-2 bg-white border border-slate-200 text-slate-400 hover:text-red-500 hover:border-red-200 hover:bg-red-50 rounded-xl transition text-[12px]"
                                                    title="移除收藏">
                                                    <i class="bi bi-bookmark-x"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-20 bg-slate-50/50 rounded-2xl border border-dashed border-slate-300">
                            <div class="bg-white w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4 shadow-sm border border-slate-100">
                                <i class="bi bi-bookmark text-2xl text-slate-300"></i>
                            </div>
                            <p class="text-slate-700 text-sm font-bold">收藏清單是空的</p>
                            <p class="text-slate-400 text-xs mt-1.5 font-medium">在地圖頁規劃時，按景點旁的書籤圖示即可收藏</p>
                            <a href="/map" class="inline-block mt-6 bg-white border border-slate-200 text-slate-700 px-6 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 transition shadow-sm">
                                <i class="bi bi-compass mr-1"></i> 前往地圖規劃
                            </a>
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- 分享行程 Modal --}}
    <div id="shareModal" class="fixed inset-0 bg-slate-900/40 hidden flex items-center justify-center z-50 backdrop-blur-sm transition-opacity">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 mx-4 transform scale-95 opacity-0 transition-all duration-300" id="shareModalContent">
            <div class="flex justify-between items-center mb-6 border-b border-slate-100 pb-4">
                <h3 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <i class="bi bi-send-fill text-indigo-500"></i> 發佈至靈感社群
                </h3>
                <button onclick="closeShareModal()" class="text-slate-400 hover:text-slate-600 transition bg-slate-50 hover:bg-slate-100 rounded-full w-8 h-8 flex items-center justify-center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <form action="{{ route('feed.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="trip_id" id="modal_trip_id">
                <input type="hidden" name="days_count" id="modal_days_count">
                <div class="space-y-4">
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-1.5">貼文標題</label>
                        <input type="text" name="title" id="modal_title" required class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none text-sm font-medium text-slate-800 placeholder:text-slate-400 transition" placeholder="給這趟旅程一個吸引人的標題">
                    </div>
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-1.5">旅遊心得 / 簡介</label>
                        <textarea name="content" rows="3" placeholder="跟大家分享這趟旅程的亮點！" class="w-full border border-slate-200 rounded-xl px-3.5 py-2.5 focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none resize-none text-sm font-medium text-slate-800 placeholder:text-slate-400 custom-scrollbar transition"></textarea>
                    </div>
                    <div>
                        <label class="block text-[13px] font-bold text-slate-700 mb-1.5">
                            <i class="bi bi-image text-indigo-400"></i> 封面圖片（選填）
                        </label>
                        <label id="image-upload-label" class="flex flex-col items-center justify-center w-full h-28 border-2 border-dashed border-slate-200 rounded-xl cursor-pointer bg-slate-50 hover:bg-indigo-50 hover:border-indigo-300 transition group relative overflow-hidden">
                            <div id="image-preview-wrap" class="absolute inset-0 hidden">
                                <img id="image-preview" src="" class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black/30 flex items-center justify-center opacity-0 group-hover:opacity-100 transition">
                                    <span class="text-white text-xs font-bold">點擊更換</span>
                                </div>
                            </div>
                            <div id="image-placeholder" class="flex flex-col items-center gap-1.5 text-slate-400 group-hover:text-indigo-500 transition">
                                <i class="bi bi-cloud-upload text-2xl"></i>
                                <span class="text-xs font-semibold">點擊上傳封面圖片</span>
                                <span class="text-[10px] text-slate-300">JPG / PNG / WEBP，最大 5MB</span>
                            </div>
                            <input type="file" name="image" id="image-input" accept="image/*" class="hidden" onchange="previewImage(this)">
                        </label>
                    </div>
                    <div class="pt-2 flex gap-3">
                        <button type="button" onclick="closeShareModal()" class="flex-1 bg-white border border-slate-200 text-slate-600 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-50 transition">取消</button>
                        <button type="submit" class="flex-1 bg-slate-800 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-700 transition shadow-sm flex justify-center items-center gap-2">
                            <i class="bi bi-rocket-takeoff"></i> 確認發佈
                        </button>
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
            resetImagePreview();
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            modal.classList.remove('hidden');
            setTimeout(() => { content.classList.remove('scale-95', 'opacity-0'); content.classList.add('scale-100', 'opacity-100'); }, 10);
        }

        function closeShareModal() {
            const modal = document.getElementById('shareModal');
            const content = document.getElementById('shareModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => { modal.classList.add('hidden'); resetImagePreview(); }, 300);
        }

        function previewImage(input) {
            if (!input.files || !input.files[0]) return;
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('image-preview').src = e.target.result;
                document.getElementById('image-preview-wrap').classList.remove('hidden');
                document.getElementById('image-placeholder').classList.add('hidden');
            };
            reader.readAsDataURL(input.files[0]);
        }

        function resetImagePreview() {
            const input = document.getElementById('image-input');
            if (input) input.value = '';
            document.getElementById('image-preview').src = '';
            document.getElementById('image-preview-wrap').classList.add('hidden');
            document.getElementById('image-placeholder').classList.remove('hidden');
        }
    </script>
</x-app-layout>
