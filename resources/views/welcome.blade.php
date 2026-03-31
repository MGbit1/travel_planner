<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>TripFlow - 智慧旅遊規劃平台</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvAAdWjnxCHy6kfojvWq4iO4wKHOl14eY&libraries=places,marker&v=beta&language=zh-TW&callback=initMap" async defer></script>

    <style>
        body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif; }
        #map { height: 100vh; width: 100%; }
        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; }
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        #search-results-panel { max-height: 0; transition: max-height 0.3s ease-out; }
        #search-results-panel.active { max-height: 400px; }
        .move-btn { opacity: 0.4; transition: opacity 0.2s; }
        .group:hover .move-btn { opacity: 1; }
        
        /* 低調質感的按鈕與標籤風格 */
        .mode-btn { transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 6px; }
        .mode-btn.active { background-color: #1e293b !important; color: white !important; font-weight: 700; border: 1px solid #0f172a; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        
        .day-tab { white-space: nowrap; transition: all 0.2s; cursor: pointer; }
        .day-tab.active { background-color: #1e293b; color: white; border-color: #0f172a; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); }
        .day-tab:hover:not(.active) { background-color: #f1f5f9; }

        .route-label { 
            background: rgba(30, 41, 59, 0.95); padding: 5px 12px; border-radius: 8px; border: 1px solid #334155; 
            color: #ffffff; font-size: 11px; font-weight: 600; box-shadow: 0 4px 12px rgba(0,0,0,0.15); 
            white-space: nowrap; pointer-events: none; display: flex; align-items: center; gap: 6px; z-index: 500;
        }
        .diff-label {
            background: rgba(255, 255, 255, 0.95); padding: 4px 8px; border-radius: 6px;
            font-size: 10px; font-weight: 600; box-shadow: 0 2px 6px rgba(0,0,0,0.08);
            pointer-events: auto; cursor: pointer; white-space: nowrap; z-index: 150;
            transition: all 0.2s ease; border: 1px solid #e2e8f0; color: #475569;
        }
        .diff-faster { color: #059669; border-color: #a7f3d0; background: #ecfdf5; }
        .diff-slower { color: #64748b; }
        .transit-step { border-left: 2px solid #e2e8f0; margin-left: 8px; padding-left: 16px; position: relative; margin-bottom: 16px; }
        .transit-step::before { content: ''; position: absolute; left: -5px; top: 4px; width: 8px; height: 8px; background: #94a3b8; border-radius: 50%; }

        @keyframes fade-in-up { 0% { opacity: 0; transform: translateY(10px); } 100% { opacity: 1; transform: translateY(0); } }
        .animate-fade-in-up { animation: fade-in-up 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased selection:bg-indigo-100 selection:text-indigo-900">

    <div id="global-loader" class="fixed inset-0 z-[999] bg-slate-50 flex flex-col items-center justify-center transition-opacity duration-500">
        <div class="w-16 h-16 relative flex items-center justify-center mb-5">
            <div class="absolute inset-0 rounded-full border-t-2 border-indigo-600 animate-spin"></div>
            <div class="absolute inset-2 rounded-full border-t-2 border-blue-400 animate-spin" style="animation-direction: reverse; animation-duration: 1.5s;"></div>
            <i class="bi bi-compass text-2xl text-slate-800"></i>
        </div>
        <h2 class="text-xl font-extrabold text-slate-800 tracking-tight mb-2">TripFlow<span class="text-indigo-600">.</span></h2>
        <p class="text-[12px] text-slate-500 font-medium animate-pulse">正在為您準備專屬地圖與行程資料...</p>
    </div>

    <div class="flex flex-col md:flex-row h-[100dvh] w-full overflow-hidden bg-slate-100">
        
        <div class="order-2 md:order-1 w-full h-[55%] md:h-full md:w-[380px] lg:w-[420px] bg-white shadow-[0_-8px_30px_rgba(0,0,0,0.08)] md:shadow-[4px_0_24px_rgba(0,0,0,0.04)] z-20 flex flex-col flex-shrink-0 border-t md:border-t-0 md:border-r border-slate-200 rounded-t-[2rem] md:rounded-none relative">
            
            <div class="md:hidden flex justify-center pt-3 pb-1 w-full shrink-0">
                <div class="w-12 h-1.5 bg-slate-200 rounded-full"></div>
            </div>

            <div class="px-6 py-4 bg-white flex justify-between items-center border-b border-slate-100 sticky top-0 z-30">
                <a href="/" class="block hover:opacity-80 transition">
                    <h1 class="text-xl font-extrabold tracking-tight text-slate-800">TripFlow<span class="text-indigo-600">.</span></h1>
                    <p class="text-slate-400 text-[10px] font-medium tracking-widest mt-0.5 uppercase">Smart Planning</p>
                </a>
                
                <div class="flex items-center gap-4">
                    @if (Route::has('login'))
                        @auth
                            <div class="flex items-center text-[12px] font-medium text-slate-500 gap-4 pr-4 border-r border-slate-200">
                                <a href="{{ route('feed.index') }}" class="hover:text-slate-900 transition flex items-center gap-1.5"><i class="bi bi-compass"></i> 社群</a>
                                <a href="/ranking" class="hover:text-slate-900 transition flex items-center gap-1.5"><i class="bi bi-trophy"></i> 榜單</a>
                            </div>
                            <div class="flex flex-col items-end">
                                <a href="{{ route('dashboard') }}" class="text-[12px] text-slate-800 font-bold hover:text-indigo-600 transition flex items-center gap-1">
                                    <i class="bi bi-person-circle"></i> 我的行程
                                </a>
                            </div>
                        @else
                            <div class="flex items-center gap-3">
                                <a href="{{ route('login') }}" class="text-[12px] font-medium text-slate-500 hover:text-slate-900 transition">登入</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-slate-800 text-white hover:bg-slate-700 px-3 py-1.5 rounded-lg text-[11px] font-semibold transition shadow-sm">免費註冊</a>
                                @endif
                            </div>
                        @endauth
                    @endif

                    <button onclick="saveFullTrip()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1.5 rounded-lg text-[12px] font-semibold shadow-sm transition flex items-center gap-1.5 ml-1">
                        <i class="bi bi-cloud-arrow-up-fill"></i> 存檔
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-7 custom-scrollbar bg-slate-50/50">
                
                <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar" id="day-tabs-container"></div>

                <div class="space-y-2.5 relative">
                    <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-1.5"><i class="bi bi-search"></i> 探索地點</label>
                    <div class="flex gap-2">
                        <input type="text" id="pac-input" class="flex-1 bg-white border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition shadow-sm placeholder:text-slate-300" placeholder="輸入景點名稱或地址...">
                        <button onclick="searchPlace()" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-700 transition shadow-sm">搜尋</button>
                    </div>
                    <div id="search-results-panel" class="hidden absolute left-0 right-0 top-full mt-2 bg-white border border-slate-200 rounded-xl shadow-xl z-30 overflow-y-auto custom-scrollbar"></div>
                </div>

                <div class="grid grid-cols-5 gap-2">
                    <button onclick="updateTravelMode('DRIVING')" class="mode-btn p-2.5 bg-white border border-slate-200 text-slate-500 rounded-xl text-[12px] font-medium active" id="btn-DRIVING"><i class="bi bi-car-front-fill text-lg"></i><span>開車</span></button>
                    <button onclick="updateTravelMode('TWO_WHEELER')" class="mode-btn p-2.5 bg-white border border-slate-200 text-slate-500 rounded-xl text-[12px] font-medium" id="btn-TWO_WHEELER"><i class="bi bi-scooter text-lg"></i><span>騎車</span></button>
                    <button onclick="updateTravelMode('TRANSIT')" class="mode-btn p-2.5 bg-white border border-slate-200 text-slate-500 rounded-xl text-[12px] font-medium" id="btn-TRANSIT"><i class="bi bi-train-front-fill text-lg"></i><span>轉乘</span></button>
                    <button onclick="updateTravelMode('BICYCLING')" class="mode-btn p-2.5 bg-white border border-slate-200 text-slate-500 rounded-xl text-[12px] font-medium" id="btn-BICYCLING"><i class="bi bi-bicycle text-lg"></i><span>單車</span></button>
                    <button onclick="updateTravelMode('WALKING')" class="mode-btn p-2.5 bg-white border border-slate-200 text-slate-500 rounded-xl text-[12px] font-medium" id="btn-WALKING"><i class="bi bi-person-walking text-lg"></i><span>步行</span></button>
                </div>

                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3 relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-1 h-full bg-indigo-500"></div>
                    <label class="text-[12px] font-bold text-slate-800 flex items-center gap-2">
                        <i class="bi bi-stars text-indigo-500"></i> AI 智慧規劃助手
                        <span class="text-[10px] font-normal text-slate-400 ml-auto">包含備案與時間精算</span>
                    </label>
                    <textarea id="ai-chat-prompt" rows="2" 
                        class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 outline-none transition resize-none custom-scrollbar placeholder:text-slate-400" 
                        placeholder="請描述您的想法，例如：明天下午想去台北車站附近喝咖啡看書..."></textarea>
                    <button onclick="askAIForItinerary()" id="ai-gen-btn" 
                        class="w-full bg-slate-800 text-white py-2.5 rounded-xl text-sm font-semibold hover:bg-slate-700 transition shadow-sm flex justify-center items-center gap-2">
                        <i class="bi bi-magic" id="ai-btn-icon"></i> <span id="ai-btn-text">生成建議路線</span>
                    </button>
                </div>
                
                <div id="route-toggles" class="hidden bg-white p-4 rounded-xl border border-slate-200 space-y-3 animate-fade-in-up">
                    <h3 class="font-semibold text-slate-700 text-sm flex justify-between items-center">
                        <span class="flex items-center gap-1.5"><i class="bi bi-signpost-split text-slate-400"></i> 路線檢視</span>
                        <label class="flex items-center gap-1.5 text-[11px] text-slate-500 cursor-pointer hover:text-slate-800 transition">
                            <input type="checkbox" id="toggle-all-routes" checked onchange="toggleAllRoutes(this.checked)" class="accent-slate-800 rounded"> 全選
                        </label>
                    </h3>
                    <div id="route-toggle-list" class="grid grid-cols-2 gap-2 text-[12px]"></div>
                </div>

                <div class="space-y-4 pt-2">
                    <div class="flex justify-between items-end border-b border-slate-200 pb-3">
                        <h2 class="font-extrabold text-slate-800 text-lg flex items-center gap-2">
                            <span id="current-day-label">Day 1 行程</span>
                        </h2>
                        <span class="text-[11px] text-slate-400 font-medium bg-slate-100 px-2 py-0.5 rounded-md" id="point-count">0 個地點</span>
                    </div>
                    
                    <button onclick="smartOptimizeRoute()" id="optimize-btn" class="hidden w-full mb-1 py-2.5 bg-white border border-indigo-200 text-indigo-600 font-semibold rounded-xl hover:bg-indigo-50 transition-colors text-[13px] flex justify-center items-center gap-2">
                        <i class="bi bi-shuffle"></i> 智能一鍵最佳化
                    </button>

                    <div id="itinerary-list" class="space-y-3 min-h-[100px]"></div>
                    
                    <button onclick="calculateRoute()" id="route-btn" class="hidden w-full bg-slate-800 text-white py-3.5 rounded-xl font-semibold text-sm hover:bg-slate-700 shadow-sm transition">
                        規劃 Day <span id="btn-day-num">1</span> 路線
                    </button>
                </div>

                <div id="ai-summary-container" class="hidden animate-fade-in-up mt-6">
                    <div id="ai-itinerary-summary"></div>
                </div>

                <div id="ai-suggestion-box" class="hidden p-0 animate-fade-in-up mt-6">
                    <div id="ai-time-suggestion"></div>
                    
                    <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm mt-3">
                        <h3 class="text-slate-800 font-bold text-[13px] mb-3 flex items-center gap-2"><i class="bi bi-info-circle text-indigo-500"></i> 導航指引與建議</h3>
                        <div id="ai-map-instruction" class="text-[12px] text-slate-600 leading-relaxed space-y-4">
                            <div id="ai-map-instruction-content"></div>
                            <div id="ai-distance-suggestion"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div> <div class="order-1 md:order-2 flex-1 relative bg-slate-100 w-full h-[45%] md:h-full">
            <div id="map" class="w-full h-full"></div>
            <button onclick="toggleTraffic()" class="absolute top-5 right-5 bg-white/90 backdrop-blur px-4 py-2.5 rounded-xl shadow-sm z-10 text-[12px] font-semibold text-slate-700 hover:bg-white hover:shadow-md transition border border-slate-200 flex items-center gap-2">
                <i class="bi bi-stoplights"></i> 即時路況
            </button>
        </div>
    </div> <script>
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        
        const currentUserId = "{{ Auth::check() ? Auth::id() : 'guest' }}"; 
        const lastUserId = sessionStorage.getItem('last_user_state');         

        if (lastUserId !== null && lastUserId !== currentUserId) {
            sessionStorage.removeItem('trip_ai_memory');
            sessionStorage.removeItem('trip_itinerary_memory');
            console.log('🔄 登入狀態改變，已自動清空所有地圖草稿');
        }
        
        sessionStorage.setItem('last_user_state', currentUserId);

        const loadedTripJson = {!! json_encode($loadedTrip ? $loadedTrip->itinerary_data : null) !!};
        const loadedTripTitle = {!! json_encode($loadedTrip ? $loadedTrip->title : null) !!};
        const loadedTripChatHistory = {!! json_encode($loadedTrip ? $loadedTrip->chat_history : null) !!};

        let map, service, geocoder, directionsService, trafficLayer;
        let itineraryData = { 1: [] };
        let currentDay = 1;
        let dayCount = 1;
        
        let aiChatHistory = {}; 
        if (loadedTripChatHistory) {
            aiChatHistory = loadedTripChatHistory; 
        } else {
            const localMemory = sessionStorage.getItem('trip_ai_memory');
            if (localMemory) {
                try { aiChatHistory = JSON.parse(localMemory); } catch (e) { aiChatHistory = {}; }
            }
        }

        let searchDrafts = {}; 
        let aiPromptDrafts = {};

        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('new') === '1') {
            sessionStorage.removeItem('trip_ai_memory');
            sessionStorage.removeItem('trip_itinerary_memory');
            aiChatHistory = {}; 
            window.history.replaceState({}, document.title, "/");
        }

        let markers = [], routeLines = [], routeLabels = [];
        let currentMode = 'DRIVING', visibleLegs = new Set(), selectedRoutesMap = {}; 
        let lastShownDetailId = null;
        let tempOptimizedItinerary = null; 
        let currentAnalysisId = 0; 

        const colorPalette = ["#3b82f6", "#10b981", "#f59e0b", "#8b5cf6", "#ec4899", "#14b8a6", "#f43f5e"];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), { center: { lat: 24.162, lng: 120.640 }, zoom: 14, mapId: "4504f8b37365c3d0" });
            service = new google.maps.places.PlacesService(map); geocoder = new google.maps.Geocoder(); directionsService = new google.maps.DirectionsService();
            trafficLayer = new google.maps.TrafficLayer();
            
            const input = document.getElementById("pac-input");
            input.addEventListener("keydown", (e) => { if (e.key === "Enter") { e.preventDefault(); searchPlace(); } });
            
            const aiInput = document.getElementById("ai-chat-prompt");
            aiInput.addEventListener("keydown", (e) => { 
                if (e.key === "Enter" && !e.shiftKey) { 
                    e.preventDefault(); 
                    askAIForItinerary(); 
                } 
            });

            const autocomplete = new google.maps.places.Autocomplete(input, { fields: ["name", "geometry", "place_id", "photos", "reviews", "types", "rating", "user_ratings_total", "formatted_address"] });
            autocomplete.addListener("place_changed", () => { const place = autocomplete.getPlace(); if (place.geometry) { processNewPlace(place); input.value = ""; } });

            if (loadedTripJson) {
                restoreLoadedTrip();
            } else {
                restoreLocalDraft();
                renderDayTabs(); 
                updateUI(); 
            }

            setTimeout(() => {
                const loader = document.getElementById('global-loader');
                if (loader) {
                    loader.classList.add('opacity-0');
                    setTimeout(() => loader.remove(), 500); 
                }
            }, 800);
        }

        function restoreLoadedTrip() {
            itineraryData = {};
            let maxDay = 1;
            let bounds = new google.maps.LatLngBounds();
            let hasPoints = false;

            for (let dayStr in loadedTripJson) {
                let dayNum = parseInt(dayStr);
                if (dayNum > maxDay) maxDay = dayNum;
                
                itineraryData[dayNum] = loadedTripJson[dayStr].map(point => {
                    let realLocation = new google.maps.LatLng(point.location.lat, point.location.lng);
                    bounds.extend(realLocation);
                    hasPoints = true;

                    return {
                        ...point,
                        location: realLocation
                    };
                });
            }
            
            dayCount = maxDay;
            currentDay = 1; 
            
            renderDayTabs();
            updateUI();
            
            if (hasPoints) {
                map.fitBounds(bounds);
                refreshMarkersOnly();
                if (itineraryData[currentDay].length >= 2) {
                    calculateRoute();
                }
            }

            setTimeout(() => {
                const label = document.getElementById('current-day-label');
                if(label) {
                    label.innerHTML = `Day 1 行程 <span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded border border-indigo-100 font-medium ml-2"><i class="bi bi-folder2-open"></i> ${loadedTripTitle}</span>`;
                }
            }, 100);
        }

        function restoreLocalDraft() {
            const localItinerary = sessionStorage.getItem('trip_itinerary_memory');
            if (localItinerary) {
                try {
                    let parsed = JSON.parse(localItinerary);
                    let maxDay = 1;
                    let bounds = new google.maps.LatLngBounds();
                    let hasPoints = false;

                    for (let dayStr in parsed) {
                        let dayNum = parseInt(dayStr);
                        if (dayNum > maxDay) maxDay = dayNum;
                        
                        itineraryData[dayNum] = parsed[dayStr].map(point => {
                            let realLocation = new google.maps.LatLng(point.location.lat, point.location.lng);
                            bounds.extend(realLocation);
                            hasPoints = true;
                            return { ...point, location: realLocation };
                        });
                    }
                    dayCount = maxDay;
                    
                    if (hasPoints) {
                        setTimeout(() => {
                            map.fitBounds(bounds);
                            refreshMarkersOnly();
                            if (itineraryData[currentDay] && itineraryData[currentDay].length >= 2) calculateRoute();
                        }, 300);
                    }
                } catch(e) { console.error("草稿還原失敗", e); }
            }
        }

        async function saveFullTrip() {
            if (!isLoggedIn) {
                alert("🔒 請先「登入或註冊」，才能將專屬行程存入您的帳號喔！");
                window.location.href = "{{ route('login') }}"; 
                return;
            }

            let hasPoints = false;
            for (let day in itineraryData) {
                if (itineraryData[day].length > 0) hasPoints = true;
            }
            if (!hasPoints) {
                alert("目前還沒有加入任何景點，請先規劃行程再存檔喔！");
                return;
            }

            const defaultTitle = loadedTripTitle ? loadedTripTitle + " (修改版)" : "我的全新旅程";
            const title = prompt("請為這趟旅程取個名字：", defaultTitle);
            if (!title) return;

            const payload = {
                title: title,
                itinerary_data: itineraryData,
                chat_history: aiChatHistory
            };

            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

            try {
                const response = await fetch('/trips', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken 
                    },
                    body: JSON.stringify(payload)
                });

                const result = await response.json();
                
                if (response.ok && result.status === 'success') {
                    sessionStorage.removeItem('trip_ai_memory'); 
                    sessionStorage.removeItem('trip_itinerary_memory');
                    alert("✅ 存檔成功！行程已安全存入您的專屬帳號資料庫！");
                } else {
                    alert("❌ 儲存失敗，請檢查後端錯誤：\n" + (result.message || JSON.stringify(result)));
                }
            } catch (error) {
                alert("🚨 發生連線錯誤，請確認 Laravel 伺服器有在運行。");
            }
        }

        function renderDayTabs() {
            const container = document.getElementById('day-tabs-container'); container.innerHTML = '';
            for (let i = 1; i <= dayCount; i++) {
                const wrapper = document.createElement('div');
                wrapper.className = "relative inline-block group shrink-0";
                
                const btn = document.createElement('button');
                const paddingRight = dayCount > 1 ? 'pr-8' : 'px-4';
                btn.className = `day-tab py-1.5 pl-4 ${paddingRight} rounded-lg text-[13px] font-semibold border border-slate-200 transition-all ${currentDay === i ? 'active' : 'bg-white text-slate-500'}`;
                btn.innerText = `Day ${i}`; 
                btn.onclick = () => switchDay(i); 
                wrapper.appendChild(btn);

                if (dayCount > 1) {
                    const delBtn = document.createElement('button');
                    delBtn.innerHTML = "<i class='bi bi-x'></i>";
                    delBtn.className = `absolute right-2 top-1/2 -translate-y-1/2 text-[14px] transition-colors ${currentDay === i ? 'text-slate-400 hover:text-white' : 'text-slate-300 hover:text-red-500'}`;
                    delBtn.onclick = (e) => { e.stopPropagation(); removeDay(i); };
                    wrapper.appendChild(delBtn);
                }

                container.appendChild(wrapper);
            }
            const addBtn = document.createElement('button'); addBtn.className = "px-3 py-1.5 rounded-lg bg-white text-slate-400 hover:text-slate-700 hover:bg-slate-100 text-[13px] font-bold border border-slate-200 transition shrink-0";
            addBtn.innerHTML = "<i class='bi bi-plus-lg'></i>"; addBtn.onclick = addNewDay; container.appendChild(addBtn);
        }

        function removeDay(dayToDelete) {
            if (dayCount <= 1) return; 
            if (!confirm(`確定要刪除 Day ${dayToDelete} 的行程嗎？`)) return;

            for (let i = dayToDelete; i < dayCount; i++) {
                itineraryData[i] = itineraryData[i + 1] || [];
                searchDrafts[i] = searchDrafts[i + 1] || '';
                aiPromptDrafts[i] = aiPromptDrafts[i + 1] || '';
                aiChatHistory[i] = aiChatHistory[i + 1] || [];
            }
            
            delete itineraryData[dayCount];
            delete searchDrafts[dayCount];
            delete aiPromptDrafts[dayCount];
            delete aiChatHistory[dayCount];
            
            dayCount--;

            if (currentDay > dayCount) currentDay = dayCount;

            clearAllRoutes(); 
            renderDayTabs(); 
            updateUI(); 
            refreshMarkersOnly(); 
            
            document.getElementById('ai-suggestion-box').classList.add('hidden'); 
            document.getElementById('ai-summary-container').classList.add('hidden');
            
            document.getElementById('current-day-label').innerText = `Day ${currentDay} 行程`; 
            document.getElementById('btn-day-num').innerText = currentDay; 

            document.getElementById('pac-input').value = searchDrafts[currentDay] || '';
            document.getElementById('ai-chat-prompt').value = aiPromptDrafts[currentDay] || '';
            document.getElementById('search-results-panel').classList.remove('active');
        }

        function switchDay(day) { 
            searchDrafts[currentDay] = document.getElementById('pac-input').value;
            aiPromptDrafts[currentDay] = document.getElementById('ai-chat-prompt').value;

            currentDay = day; 
            
            if(!itineraryData[currentDay]) itineraryData[currentDay] = [];
            
            clearAllRoutes(); 
            renderDayTabs(); 
            updateUI(); 
            refreshMarkersOnly(); 
            
            document.getElementById('ai-suggestion-box').classList.add('hidden'); 
            document.getElementById('ai-summary-container').classList.add('hidden');
            
            let titleTag = (loadedTripTitle && currentDay === 1) ? `<span class="text-[10px] bg-indigo-50 text-indigo-600 px-2 py-1 rounded border border-indigo-100 font-medium ml-2"><i class="bi bi-folder2-open"></i> ${loadedTripTitle}</span>` : '';
            document.getElementById('current-day-label').innerHTML = `Day ${currentDay} 行程 ${titleTag}`; 
            
            document.getElementById('btn-day-num').innerText = currentDay; 

            document.getElementById('pac-input').value = searchDrafts[currentDay] || '';
            document.getElementById('ai-chat-prompt').value = aiPromptDrafts[currentDay] || '';
            document.getElementById('search-results-panel').classList.remove('active');
            
            if (itineraryData[currentDay].length >= 2) {
                calculateRoute();
            }
        }
        
        function addNewDay() { dayCount++; itineraryData[dayCount] = []; switchDay(dayCount); }

        function searchPlace() {
            const query = document.getElementById("pac-input").value; if (!query) return;
            const panel = document.getElementById("search-results-panel"); panel.innerHTML = `<div class="p-4 text-xs text-slate-400">正在搜尋...</div>`; panel.classList.remove("hidden"); panel.classList.add("active");
            service.textSearch({ query, location: map.getCenter(), radius: '5000', language: 'zh-TW' }, (results, status) => { if (status === 'OK') renderSearchResults(results); else panel.innerHTML = `<div class="p-4 text-xs text-red-500 font-medium">找不到符合的地點。</div>`; });
        }

        function renderSearchResults(results) {
            const panel = document.getElementById("search-results-panel"); panel.innerHTML = "";
            results.slice(0, 5).forEach(place => {
                const div = document.createElement("div"); div.className = "p-4 border-b border-slate-100 hover:bg-slate-50 cursor-pointer flex items-start gap-3 transition";
                div.innerHTML = `<div class="mt-0.5 text-slate-400"><i class="bi bi-geo-alt-fill"></i></div><div class="flex-1 overflow-hidden"><div class="text-[13px] font-semibold text-slate-800 truncate">${place.name}</div><div class="text-[11px] text-slate-400 truncate mt-0.5">${place.formatted_address}</div></div>`;
                div.onclick = () => { fetchFullDetails(place.place_id); panel.classList.remove("active"); document.getElementById("pac-input").value = ""; }; panel.appendChild(div);
            });
        }

        function fetchFullDetails(placeId) { service.getDetails({ placeId, fields: ["name", "geometry", "place_id", "photos", "reviews", "types", "rating", "user_ratings_total", "formatted_address"] }, (place, status) => { if (status === 'OK') processNewPlace(place); }); }

        function processNewPlace(place) {
            if(!itineraryData[currentDay]) itineraryData[currentDay] = [];
            itineraryData[currentDay].push({ id: Date.now(), name: place.name || place.formatted_address, location: place.geometry.location, photo: place.photos ? place.photos[0].getUrl({ maxWidth: 400 }) : null, reviews: place.reviews || [], types: place.types || [], rating: place.rating || 0, user_ratings_total: place.user_ratings_total || 0, note: "", isLocked: false });
            updateUI(); map.panTo(place.geometry.location); clearAllRoutes(); refreshMarkersOnly();
        }

        function clearAllRoutes() { 
            routeLines.forEach(l => l.setMap(null)); 
            routeLabels.forEach(l => l.setMap(null)); 
            routeLines = []; 
            routeLabels = []; 
            document.getElementById('ai-suggestion-box').classList.add('hidden'); 
            document.getElementById('route-toggles').classList.add('hidden'); 
            visibleLegs.clear(); 
            selectedRoutesMap = {}; 
        }

        function refreshMarkersOnly() {
            markers.forEach(m => m.setMap(null)); markers = []; const counts = {}; const currentItinerary = itineraryData[currentDay] || [];
            currentItinerary.forEach((p, index) => {
                const key = `${p.location.lat().toFixed(6)},${p.location.lng().toFixed(6)}`; let lat = p.location.lat(), lng = p.location.lng();
                if (counts[key]) { lat += (counts[key] * 0.00022); lng += (counts[key] * 0.00022); counts[key]++; } else { counts[key] = 1; }
                const glyph = document.createElement('div'); glyph.className = 'bg-slate-800 text-white w-7 h-7 rounded-full flex items-center justify-center font-bold text-[13px] shadow-sm border-2 border-white'; glyph.innerText = (index + 1).toString();
                markers.push(new google.maps.marker.AdvancedMarkerElement({ map, position: {lat, lng}, content: glyph }));
            });
        }

        async function calculateRoute() {
            const currentItinerary = itineraryData[currentDay] || []; if (currentItinerary.length < 2) return;
            clearAllRoutes(); document.getElementById('route-toggles').classList.remove('hidden');
            
            const aiBox = document.getElementById('ai-suggestion-box');
            aiBox.classList.remove('hidden'); 
            
            document.getElementById('ai-map-instruction-content').innerHTML = `<div class="text-[12px] text-slate-400 mb-2 flex items-center gap-1.5"><i class="bi bi-arrow-repeat animate-spin"></i> 正在規劃 Day ${currentDay} 最佳路線...</div>`;
            document.getElementById('ai-distance-suggestion').innerHTML = '';
            document.getElementById('ai-time-suggestion').innerHTML = '';

            for(let i=0; i < currentItinerary.length - 1; i++) { visibleLegs.add(i); selectedRoutesMap[i] = { index: 0, result: null }; }
            updateRouteToggleUI(); 
            for (let i = 0; i < currentItinerary.length - 1; i++) {
                const results = await requestRouteByMode(currentItinerary[i].location, currentItinerary[i+1].location, currentMode);
                if (results && results.routes) { 
                    selectedRoutesMap[i].result = results; drawLeg(results.routes, i); 
                } else { 
                    document.getElementById('ai-map-instruction-content').innerHTML = `
                        <div class="p-3 bg-red-50/50 rounded-lg border border-red-100 mb-2">
                            <span class="text-red-600 font-semibold text-[13px] flex items-center gap-1.5">
                                <i class="bi bi-exclamation-circle"></i> 第 ${i+1} 段路線計算失敗
                            </span>
                            <div class="text-[11px] text-red-500/80 mt-2 space-y-1">
                                <p><strong>常見原因：</strong></p>
                                <p>1. 該國家/地區不支援 Google 導航 (如：韓國受法規限制)。</p>
                                <p>2. 兩地之間無法透過該交通方式到達 (如：跨海、無道路相連)。</p>
                                <p>3. 該地區缺乏大眾運輸資料。</p>
                            </div>
                        </div>`; 
                    return; 
                }
            }
            renderAISuggestions(); updateRouteVisibility(); 
            checkOptimalRouteSuggestion(); 
            analyzeTimeSuitabilityInBackground(); 
        }

        async function analyzeTimeSuitabilityInBackground() {
            const currentItinerary = itineraryData[currentDay] || [];
            if (currentItinerary.length < 2) return;

            let myAnalysisId = ++currentAnalysisId;
            let placesList = currentItinerary.map((p, index) => `第 ${index + 1} 站：${p.name}`).join(' -> ');
            
            let promptValue = `使用者目前的行程順序是：${placesList}。請簡短檢查這個順序的「日夜時間合適度」。請將所有需要提醒的地點統整成「一段流暢的完整建議」，字數約 50 字以內。如果時間安排看起來很合理，請直接說「日夜時間安排看起來很順暢喔！」。絕對不要使用條列式。`;

            try {
                const box = document.getElementById('ai-time-suggestion');
                if(!box) return;
                
                box.innerHTML = `<div class="mt-3 text-indigo-400 text-[11px] animate-pulse flex items-center gap-1.5"><i class="bi bi-cpu"></i> AI 正在評估最佳造訪時間...</div>`;

                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ prompt: promptValue, mode: currentMode, history: [], all_itineraries: {} })
                });

                if (myAnalysisId !== currentAnalysisId) return; 

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    box.innerHTML = `<div class="mt-3 p-3 bg-slate-50 text-slate-700 text-[12px] font-medium rounded-xl border border-slate-200 shadow-sm leading-relaxed">
                        <div class="flex items-center gap-1.5 mb-1.5 text-[12px] font-bold text-slate-800"><i class="bi bi-clock-history"></i> 時間檢核：</div>
                        ${data.ai_message}
                    </div>`;
                } else {
                    box.innerHTML = '';
                }
            } catch (error) {
                const box = document.getElementById('ai-time-suggestion');
                if(box) box.innerHTML = '';
            }
        }

        async function smartOptimizeRoute() {
            const currentItinerary = itineraryData[currentDay] || [];
            if (currentItinerary.length < 3) return alert("行程太少，無需最佳化！");

            let placesList = currentItinerary.map((p, index) => {
                return `第 ${index + 1} 站：${p.name} ${p.isLocked ? '(此站順序【絕對不可變動】)' : ''}`;
            }).join('\n');

            let promptValue = `我已經選定了以下 ${currentItinerary.length} 個景點（依照目前順序）：\n${placesList}\n\n請發揮專業導遊的能力，根據「各景點最適合的日夜時間（如夜市/看夜景必須在晚上）」與「交通順路程度」幫我重新排序，找出最完美的走法。\n\n【嚴格約束條件】：\n1. 標示為【絕對不可變動】的景點，絕對不能改變它在清單中的順序！\n2. 請務必使用原景點名稱，不要新增或刪除。\n3. 給我排好的清單。`;

            const btn = document.getElementById('optimize-btn');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.classList.add('opacity-50', 'cursor-not-allowed');
            btn.innerHTML = `<i class="bi bi-arrow-repeat animate-spin"></i> 系統深度重排中...`;

            try {
                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ prompt: promptValue, mode: currentMode, history: [], all_itineraries: {} })
                });

                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    let newOrder = [];
                    let unmatched = [...currentItinerary];
                    
                    let suggestions = data.days && data.days.length > 0 ? data.days[0].suggestions : (data.suggestions || []);

                    suggestions.forEach(aiItem => {
                        let matchIndex = unmatched.findIndex(p => aiItem.name.includes(p.name) || p.name.includes(aiItem.name) || aiItem.name === p.name);
                        if (matchIndex !== -1) {
                            let modeEmoji = '🚗';
                            if (data.travel_mode === 'TWO_WHEELER') modeEmoji = '🛵';
                            else if (data.travel_mode === 'TRANSIT') modeEmoji = '🚌';
                            else if (data.travel_mode === 'BICYCLING') modeEmoji = '🚲';
                            else if (data.travel_mode === 'WALKING') modeEmoji = '🚶';

                            let travel = (newOrder.length === 0) ? '📍 出發點' : (aiItem.travel_time ? `${modeEmoji} ${aiItem.travel_time}` : '');
                            let stay = aiItem.stay_time ? `⏱️ ${aiItem.stay_time}` : '';
                            let cost = aiItem.cost_estimate ? `💰 ${aiItem.cost_estimate}` : '';
                            let reason = aiItem.reason ? `💡 ${aiItem.reason}` : '';
                            
                            unmatched[matchIndex].ai_description = [travel, stay, cost, reason].filter(Boolean).join(' ｜ ');
                            newOrder.push(unmatched[matchIndex]);
                            unmatched.splice(matchIndex, 1);
                        }
                    });

                    newOrder = newOrder.concat(unmatched);
                    itineraryData[currentDay] = newOrder;
                    
                    updateUI(); 
                    refreshMarkersOnly(); 
                    calculateRoute();
                    
                    alert("✨ 最佳化完成！");
                } else {
                    alert("❌ 最佳化失敗：" + (data.message || "請檢查 API 設定"));
                }
            } catch (error) {
                alert("🚨 連線異常，請確認伺服器運作中");
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-50', 'cursor-not-allowed');
                btn.innerHTML = originalText;
            }
        }

        async function requestRouteByMode(origin, dest, mode) {
            let apiMode = google.maps.TravelMode[mode] || mode; 
            let request = { origin: origin, destination: dest, travelMode: apiMode, provideRouteAlternatives: true };
            
            if (mode === 'DRIVING' || mode === 'TWO_WHEELER') { 
                request.drivingOptions = { departureTime: new Date(), trafficModel: google.maps.TrafficModel.BEST_GUESS }; 
            }
            
            return new Promise((resolve) => { 
                directionsService.route(request, (res, status) => {
                    if (status === 'OK') {
                        resolve(res);
                    } else if (mode === 'TWO_WHEELER') {
                        request.travelMode = google.maps.TravelMode.DRIVING;
                        request.avoidHighways = true;
                        request.avoidTolls = true;
                        directionsService.route(request, (fallbackRes, fStatus) => resolve(fStatus === 'OK' ? fallbackRes : null));
                    } else {
                        resolve(null);
                    }
                }); 
            });
        }

        function getTrafficColor(route, defaultColor) {
            if (route.legs[0].duration_in_traffic) {
                const ratio = route.legs[0].duration_in_traffic.value / route.legs[0].duration.value;
                if (ratio > 1.25) return '#e11d48'; if (ratio > 1.0) return '#d97706'; return '#059669';                    
            } return defaultColor;
        }

        function drawLeg(routes, legIndex) {
            const legColor = colorPalette[legIndex % colorPalette.length];
            routes.forEach((route, rIdx) => {
                const isSel = (rIdx === selectedRoutesMap[legIndex].index); const off = (legIndex * 0.00015) + (rIdx * 0.00005);
                const pathCoords = route.overview_path.map(c => ({ lat: c.lat() + off, lng: c.lng() + off }));
                const strokeColor = isSel ? getTrafficColor(route, legColor) : legColor;
                const lineOptions = { path: pathCoords, strokeColor: strokeColor, strokeOpacity: isSel ? 1.0 : 0.4, strokeWeight: isSel ? 7 : 4, zIndex: isSel ? 100 : 10, map: map };
                if (isSel) { lineOptions.icons = [{ icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 3, strokeWeight: 2, fillColor: '#FFFFFF', fillOpacity: 1, strokeColor: '#1e293b' }, offset: '95%' }]; }
                const polyline = new google.maps.Polyline(lineOptions); polyline.originalColor = legColor; polyline.routeData = route; 
                polyline.addListener('click', () => { selectedRoutesMap[legIndex].index = rIdx; refreshGraphics(); renderAISuggestions(); });
                routeLines.push(polyline); polyline.legIndex = legIndex; polyline.routeIndex = rIdx; createRouteLabel(route, legIndex, rIdx);
            });
        }

        function createRouteLabel(route, legIndex, routeIndex) {
            const labelDiv = document.createElement('div'); const pathPoints = route.overview_path; const posStart = pathPoints[Math.floor(pathPoints.length / 4)];
            const marker = new google.maps.marker.AdvancedMarkerElement({ map, position: posStart, content: labelDiv });
            marker.legIndex = legIndex; marker.routeIndex = routeIndex; marker.legData = route.legs[0]; routeLabels.push(marker); updateSingleLabel(marker, legIndex);
        }

        function updateSingleLabel(marker, legIndex) {
            const selIdx = selectedRoutesMap[legIndex].index; if (!selectedRoutesMap[legIndex].result) return;
            const priVal = selectedRoutesMap[legIndex].result.routes[selIdx].legs[0].duration.value; const curLeg = marker.legData;
            if (marker.routeIndex === selIdx) {
                let timeText = curLeg.duration.text.replace("mins", "分"); let colorClass = "text-white";
                if (curLeg.duration_in_traffic) {
                    timeText = curLeg.duration_in_traffic.text.replace("mins", "分"); const ratio = curLeg.duration_in_traffic.value / curLeg.duration.value;
                    if (ratio > 1.25) colorClass = "text-rose-400"; else if (ratio > 1.0) colorClass = "text-amber-400"; else colorClass = "text-emerald-400";
                }
                marker.content.className = 'route-label'; marker.content.innerHTML = `<span>${legIndex+1}➔${legIndex+2}</span><span class="text-slate-500">|</span><span class="font-normal text-[10px]">${curLeg.distance.text}</span><span class="text-slate-500">|</span><span class="${colorClass}">${timeText}</span>`; marker.zIndex = 500;
            } else {
                const diff = Math.round((curLeg.duration.value - priVal) / 60); marker.content.className = `diff-label ${diff < 0 ? 'diff-faster' : 'diff-slower'}`; marker.content.innerText = diff < 0 ? `省 ${Math.abs(diff)} 分` : (diff > 0 ? `慢 ${diff} 分` : "同時間");
                marker.zIndex = 200; marker.content.onclick = (e) => { e.stopPropagation(); selectedRoutesMap[legIndex].index = marker.routeIndex; refreshGraphics(); renderAISuggestions(); };
            }
        }

        function refreshGraphics() { 
            routeLines.forEach(l => { 
                const isSel = (l.routeIndex === selectedRoutesMap[l.legIndex].index); const strokeColor = isSel ? getTrafficColor(l.routeData, l.originalColor) : l.originalColor;
                const newOptions = { strokeColor: strokeColor, strokeOpacity: isSel ? 1.0 : 0.4, strokeWeight: isSel ? 7 : 4, zIndex: isSel ? 100 : 10 };
                if (isSel) { newOptions.icons = [{ icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 3, strokeWeight: 2, fillColor: '#FFFFFF', fillOpacity: 1, strokeColor: '#1e293b' }, offset: '95%' }]; } else { newOptions.icons = []; } l.setOptions(newOptions);
            }); routeLabels.forEach(m => updateSingleLabel(m, m.legIndex)); updateRouteVisibility(); 
        }

        function checkOptimalRouteSuggestion() {
            const currentItinerary = itineraryData[currentDay] || []; 
            if (currentItinerary.length < 3 || currentMode === 'TRANSIT') return;

            let optMode = (currentMode === 'TWO_WHEELER') ? 'TWO_WHEELER' : currentMode;
            let request = { origin: currentItinerary[0].location, destination: currentItinerary[currentItinerary.length - 1].location, waypoints: currentItinerary.slice(1, -1).map(p => ({ location: p.location, stopover: true })), optimizeWaypoints: true, travelMode: google.maps.TravelMode[optMode] || optMode };
            
            if (currentMode === 'TWO_WHEELER') { request.travelMode = google.maps.TravelMode.DRIVING; request.avoidHighways = true; request.avoidTolls = true; }
            
            directionsService.route(request, (res, status) => {
                if (status === 'OK') {
                    const opt = res.routes[0].waypoint_order; let swap = ""; let hasLocks = currentItinerary.some(item => item.isLocked);
                    for(let i=0; i<opt.length; i++) {
                        if(opt[i] !== i) {
                            let originalIndex = i + 1; let targetIndex = opt[i] + 1;
                            if (currentItinerary[originalIndex].isLocked || currentItinerary[targetIndex].isLocked) { continue; }
                            swap = `將 <span class="font-bold">第 ${originalIndex + 1} 站</span> 與 <span class="font-bold">第 ${targetIndex + 1} 站</span> 互換更順路！`; break; 
                        }
                    }
                    
                    let suggestionHTML = "";
                    if (swap) {
                        suggestionHTML += `<div class="mt-3 p-3 bg-slate-50 text-slate-700 rounded-xl border border-slate-200 leading-relaxed shadow-sm"><span class="font-bold text-slate-800"><i class="bi bi-geo-alt"></i> 路線建議：</span>${swap} <br><span class="text-slate-400 text-[11px] mt-1 block">(可點擊上方「智能一鍵最佳化」自動排序)</span></div>`; 
                    } else if (hasLocks) {
                        suggestionHTML += `<div class="mt-3 p-2 bg-slate-50 text-slate-500 text-[11px] rounded-lg border border-slate-200"><i class="bi bi-lock"></i> 在您鎖定的條件下，順序已達最佳化。</div>`;
                    } else {
                        suggestionHTML += `<div class="mt-3 p-2 bg-slate-50 text-slate-500 text-[11px] rounded-lg border border-slate-200"><i class="bi bi-check2-circle"></i> 目前順序已經是最順路的囉！</div>`;
                    }

                    const distBox = document.getElementById('ai-distance-suggestion');
                    if(distBox) distBox.innerHTML = suggestionHTML;
                }
            });
        }

        function renderAISuggestions() {
            const currentItinerary = itineraryData[currentDay] || []; 
            const box = document.getElementById('ai-map-instruction-content'); 
            if(!box) return;
            
            let html = "";
            if (currentMode === 'TRANSIT') {
                for (let i = 0; i < currentItinerary.length - 1; i++) {
                    if (!visibleLegs.has(i) || !selectedRoutesMap[i].result) continue; const leg = selectedRoutesMap[i].result.routes[selectedRoutesMap[i].index].legs[0];
                    html += `<div class="mb-4 border-b border-slate-100 pb-3"><div class="font-semibold text-slate-700 mb-2">段落 ${i + 1}：${currentItinerary[i].name} <i class="bi bi-arrow-right text-slate-300 mx-1"></i> ${currentItinerary[i+1].name}</div>`;
                    leg.steps.forEach(step => {
                        const dur = step.duration.text.replace("mins", "分鐘");
                        if (step.travel_mode === 'TRANSIT') { html += `<div class="transit-step"><span class="text-indigo-600 font-bold text-[12px]"><i class="bi bi-train-front-fill"></i> 搭乘 ${step.transit.line.short_name || step.transit.line.name}</span><br><span class="text-[11px] text-slate-500">於 ${step.transit.departure_stop.name} 上車 (約 ${dur})</span></div>`; } else { html += `<div class="transit-step"><span class="text-slate-500 text-[11px]"><i class="bi bi-person-walking"></i> ${step.instructions.replace(/Walk to /i, "步行至 ")}</span></div>`; }
                    }); html += `</div>`;
                } box.innerHTML = html || "<span class='text-slate-400'>已規劃最佳轉乘方案。</span>"; 
            } else { 
                box.innerHTML = `<span class='flex items-center gap-1.5 text-slate-500'><i class="bi bi-cursor"></i> 點擊地圖上的淺色線條，可切換不同替代路線。</span>`; 
            }
        }

        function toggleDetail(index) { 
            const currentItinerary = itineraryData[currentDay] || []; 
            const targetContainer = document.getElementById(`item-detail-${index}`);
            
            if (lastShownDetailId === currentItinerary[index].id && !targetContainer.classList.contains('hidden')) { 
                targetContainer.classList.add('hidden'); lastShownDetailId = null; 
            } else { 
                for(let i=0; i<currentItinerary.length; i++) { const el = document.getElementById(`item-detail-${i}`); if(el) el.classList.add('hidden'); }
                targetContainer.classList.remove('hidden'); lastShownDetailId = currentItinerary[index].id; showPlaceDetail(currentItinerary[index], targetContainer); 
            } 
        }

        async function showPlaceDetail(point, container) {
            const isDashboardOrDeparture = point.name.includes("出發") || point.name.includes("回家") || point.name.includes("巷") || point.name.includes("號") || point.name.includes("住家") || point.name.includes("返程");

            if (!point.photo && !point.place_id && !isDashboardOrDeparture) {
                container.innerHTML = `<div class="py-6 text-center text-slate-400 text-[11px] font-medium flex flex-col items-center gap-2"><i class="bi bi-arrow-repeat animate-spin text-lg"></i> 載入真實照片與評價中...</div>`;
                await new Promise((resolve) => {
                    service.textSearch({ query: point.name, location: point.location, radius: '1000' }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            service.getDetails({ placeId: results[0].place_id, fields: ["photos", "reviews", "types", "rating", "place_id"] }, (place, dStatus) => {
                                if (dStatus === 'OK') {
                                    point.place_id = place.place_id; point.photo = place.photos ? place.photos[0].getUrl({ maxWidth: 400 }) : null;
                                    point.reviews = place.reviews || []; if(place.types) point.types = place.types;
                                } resolve();
                            });
                        } else { resolve(); }
                    });
                });
            }

            const hour = new Date().getHours(); const types = point.types || []; let cat = "景點", advice = "環境穩定舒適。";
            if (types.some(t => ['school', 'university'].includes(t))) { cat = "教育設施"; advice = "內部可能不開放參訪，非教職員請留意。"; } 
            else if (types.some(t => ['restaurant', 'cafe', 'food'].includes(t))) { cat = "餐飲場所"; if ((hour >= 11 && hour <= 13) || (hour >= 17 && hour <= 19)) advice = "正值用餐尖峰，建議提早訂位。"; } 
            else if (types.includes('premise') || types.includes('street_address') || types.length === 0 || isDashboardOrDeparture) { cat = "特殊地點"; advice = "可能是私人區域，請留意周遭規範。"; }
            
            let revHtml = ""; if (point.reviews && point.reviews.length > 0) {
                let p = [], c = []; point.reviews.forEach(r => { if (r.rating >= 4 && p.length < 2) p.push(r.text.substring(0, 50)); if (r.rating <= 3 && c.length < 2) c.push(r.text.substring(0, 50)); });
                revHtml = `<div class="mt-3 grid grid-cols-2 gap-2"><div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100"><p class="text-slate-700 font-semibold text-[11px] mb-1"><i class="bi bi-hand-thumbs-up text-emerald-500"></i> 優點</p><ul class="text-[10px] text-slate-500 space-y-1">${p.map(x=>`<li class="truncate">• ${x}</li>`).join('') || '<li>無特別紀錄</li>'}</ul></div><div class="bg-slate-50 p-2.5 rounded-lg border border-slate-100"><p class="text-slate-700 font-semibold text-[11px] mb-1"><i class="bi bi-exclamation-circle text-amber-500"></i> 留意</p><ul class="text-[10px] text-slate-500 space-y-1">${c.map(x=>`<li class="truncate">• ${x}</li>`).join('') || '<li>無特殊狀況</li>'}</ul></div></div>`;
            } 
            
            container.innerHTML = `
                <div class="mb-2"><span class="bg-slate-100 text-slate-500 px-2 py-0.5 rounded text-[10px] border border-slate-200 font-medium">${cat}</span></div>
                ${point.photo ? `<img src="${point.photo}" class="rounded-xl mb-3 w-full h-32 object-cover border border-slate-100">` : ''}
                <div class="p-2.5 bg-slate-50 rounded-lg text-[11px] flex gap-2 border border-slate-100">
                    <div class="flex-shrink-0 text-indigo-400"><i class="bi bi-stars"></i></div>
                    <div class="flex-1 text-slate-600 leading-relaxed">${advice}</div>
                </div>
                ${revHtml}
            `;
        }

        function editNote(id) { const currentItinerary = itineraryData[currentDay]; const item = currentItinerary.find(p => p.id === id); if (item) { const newNote = prompt(`為「${item.name}」加上備忘錄：`, item.note || ""); if (newNote !== null) { item.note = newNote; updateUI(); } } }

        function toggleLock(index) { const currentItinerary = itineraryData[currentDay]; if (!currentItinerary || !currentItinerary[index]) return; currentItinerary[index].isLocked = !currentItinerary[index].isLocked; updateUI(); if (routeLines.length > 0) calculateRoute(); }

        // 💡 更新：重新設計景點卡片的 UI
        function updateUI() {
            sessionStorage.setItem('trip_itinerary_memory', JSON.stringify(itineraryData));
            const currentItinerary = itineraryData[currentDay] || []; 
            const list = document.getElementById('itinerary-list'); 
            document.getElementById('point-count').innerText = `${currentItinerary.length} 個地點`; 
            document.getElementById('route-btn').classList.toggle('hidden', currentItinerary.length < 2);
            document.getElementById('optimize-btn').classList.toggle('hidden', currentItinerary.length < 3);

            if (currentItinerary.length === 0) { list.innerHTML = `<div class="text-center text-slate-400 py-10 text-[12px] border-2 border-dashed border-slate-200 rounded-2xl bg-white/50">Day ${currentDay} 尚未新增地點</div>`; return; }
            
            list.innerHTML = currentItinerary.map((p, i) => {
                const isLockedClass = p.isLocked ? 'border-red-200 bg-red-50/20' : 'border-slate-200 bg-white';
                const hasParkingNote = p.ai_description && (p.ai_description.includes('停車') || p.ai_description.includes('找車位'));
                
                // 💡 新增：判斷這個景點是不是「住宿 (lodging)」
                const isLodging = p.types && p.types.includes('lodging');
                
                // 💡 修改：改用標準的 Google Maps API 連結以防在編輯器中報錯
                const mapsUrlBase = "https://www.google.com/maps/dir/?api=1&destination=";
                
                return `
                <div class="${isLockedClass} border rounded-2xl p-4 shadow-sm group animate-in slide-in-from-left duration-200 relative">
                    ${p.isLocked ? `<div class="absolute -left-1.5 -top-1.5 bg-red-50 text-red-500 rounded-full w-5 h-5 flex items-center justify-center border border-red-200 text-[10px] shadow-sm"><i class="bi bi-lock-fill"></i></div>` : ''}
                    <div class="flex justify-between items-start w-full">
                        <div class="flex-1 overflow-hidden pr-2">
                            <p class="text-[10px] text-slate-400 font-semibold tracking-widest mb-1 uppercase">Stop ${i+1}</p>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-800 text-[14px] truncate">${p.name}</p>
                                
                                <button onclick="toggleDetail(${i})" class="text-slate-400 hover:text-indigo-600 transition flex-shrink-0" title="查看景點資訊">
                                    <i class="bi bi-info-circle"></i>
                                </button>
                                
                                <a href="${mapsUrlBase}${p.location.lat()},${p.location.lng()}" target="_blank" class="text-slate-400 hover:text-emerald-500 transition flex-shrink-0" title="開啟 Google 導航">
                                    <i class="bi bi-cursor-fill"></i>
                                </a>

                                ${isLodging ? `
                                <a href="https://www.agoda.com/zh-tw/search?text=${encodeURIComponent(p.name)}" target="_blank" class="text-slate-400 hover:text-sky-500 transition flex-shrink-0" title="一鍵搜出好房價 (Agoda)">
                                    <i class="bi bi-building-check"></i>
                                </a>
                                ` : ''}
                            </div>
                            
                            ${p.ai_description ? `
                                <div class="mt-2 bg-slate-50 text-slate-600 text-[10px] px-2 py-1.5 rounded inline-block border border-slate-100 font-medium">
                                    <i class="bi bi-lightbulb text-amber-500 mr-1"></i> ${p.ai_description}
                                </div>
                            ` : ''}

                            ${p.parking_mode === 'INTERNAL' ? `
                                <a href="${mapsUrlBase}${encodeURIComponent(p.name)}" target="_blank" class="mt-2 flex items-center gap-1.5 text-emerald-600 font-semibold bg-emerald-50 px-2 py-1 rounded border border-emerald-100 transition hover:bg-emerald-100" style="font-size: 10px;">
                                    <i class="bi bi-check-circle-fill"></i> ✅ 附設停車場 (直接導航)
                                </a>
                            ` : p.parking_mode === 'EXTERNAL' ? `
                                <a href="${mapsUrlBase}${encodeURIComponent(p.name + ' 停車場')}" target="_blank" class="mt-2 flex items-center gap-1.5 text-amber-600 font-semibold bg-amber-50 px-2 py-1 rounded border border-amber-100 transition hover:bg-amber-100" style="font-size: 10px;">
                                    <i class="bi bi-search"></i> 🔍 搜尋附近停車場
                                </a>
                            ` : hasParkingNote ? `
                                <a href="${mapsUrlBase}${encodeURIComponent(p.name + ' 停車場')}" target="_blank" class="mt-2 flex items-center gap-1.5 text-slate-500 font-medium" style="font-size: 10px;">
                                    <i class="bi bi-p-circle"></i> 需自行在附近尋找停車位
                                </a>
                            ` : ''}
                            ${p.note ? `<p class="text-[11px] text-indigo-600 mt-2 flex items-center gap-1.5 font-medium bg-indigo-50/50 p-1.5 rounded"><i class="bi bi-card-text"></i> ${p.note}</p>` : ''}
                        </div>
                        
                        <div class="flex flex-col items-end gap-1 opacity-20 group-hover:opacity-100 transition-opacity">
                            <div class="flex bg-slate-50 rounded-lg border border-slate-100 overflow-hidden">
                                <button onclick="moveItem(${i}, -1)" class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-slate-200 ${i === 0 ? 'invisible' : ''}"><i class="bi bi-chevron-up text-[10px]"></i></button>
                                <button onclick="moveItem(${i}, 1)" class="p-1.5 text-slate-400 hover:text-slate-800 hover:bg-slate-200 ${i === currentItinerary.length-1 ? 'invisible' : ''}"><i class="bi bi-chevron-down text-[10px]"></i></button>
                            </div>
                            <div class="flex gap-1 mt-1">
                                <button onclick="toggleLock(${i})" class="p-1.5 text-slate-400 hover:text-red-500 rounded-md hover:bg-red-50"><i class="bi ${p.isLocked ? 'bi-unlock' : 'bi-lock'} text-[12px]"></i></button>
                                <button onclick="editNote(${p.id})" class="p-1.5 text-slate-400 hover:text-indigo-500 rounded-md hover:bg-indigo-50"><i class="bi bi-pencil-square text-[12px]"></i></button>
                                <button onclick="removeItem(${p.id})" class="p-1.5 text-slate-400 hover:text-red-500 rounded-md hover:bg-red-50"><i class="bi bi-trash3 text-[12px]"></i></button>
                            </div>
                        </div>
                    </div>
                    <div id="item-detail-${i}" class="hidden w-full mt-3 pt-3 border-t border-slate-100 animate-fade-in-up"></div>
                </div>`;
            }).join('');
        }

        async function askAIForItinerary() {
            const promptValue = document.getElementById('ai-chat-prompt').value;
            if (!promptValue) return alert("請輸入您的想法喔！");

            // 💡 新增：偷偷在背後加上「系統強制緊箍咒」，逼 AI 給出具體實體
            const enhancedPrompt = promptValue + "\n\n(系統強制指令：1. 如果使用者要求尋找住宿，請直接給出「具體且真實存在的飯店/旅館名稱」。2. 絕對不要給出「某某周邊」、「某某考察」、「尋找住宿」這種模糊的行程動作。3. 景點名稱請保持乾淨，不要在名稱後面加括號補充說明。)";

            const btn = document.getElementById('ai-gen-btn');
            const btnText = document.getElementById('ai-btn-text');
            const icon = document.getElementById('ai-btn-icon');
            
            btn.disabled = true;
            btn.classList.add('opacity-70', 'cursor-not-allowed');
            icon.className = "bi bi-arrow-repeat animate-spin";
            btnText.innerText = "正在為您規劃最佳路線...";

            try {
                if (!aiChatHistory[currentDay]) aiChatHistory[currentDay] = [];
                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
                    body: JSON.stringify({ prompt: enhancedPrompt, mode: currentMode, history: aiChatHistory[currentDay], all_itineraries: itineraryData, current_day: currentDay })
                });

                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    aiChatHistory[currentDay].push({ role: 'user', text: promptValue });
                    aiChatHistory[currentDay].push({ role: 'model', text: `行程規劃完畢。總結：${data.ai_message}` });
                    sessionStorage.setItem('trip_ai_memory', JSON.stringify(aiChatHistory));

                    if (data.travel_mode) {
                        currentMode = data.travel_mode;
                        document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active'));
                        const activeBtn = document.getElementById(`btn-${data.travel_mode}`);
                        if (activeBtn) activeBtn.classList.add('active');
                    }

                    if (data.days && data.days.length > 0) {
                        let highestDay = 1;
                        for (let dayObj of data.days) {
                            let dayNum = parseInt(dayObj.day) || 1;
                            if (dayNum > highestDay) highestDay = dayNum;
                            let newPoints = [];
                            for (let index = 0; index < dayObj.suggestions.length; index++) {
                                let item = dayObj.suggestions[index];
                                let isDashboard = item.name.includes('出發') || item.name.includes('回家') || item.name.includes('巷') || item.name.includes('號') || item.name.includes('住家') || item.name.includes('返程');
                                
                                // 💡 新增：AI 住宿雷達！自動偵測名稱是否包含飯店關鍵字
                                let isHotel = item.name.includes('飯店') || item.name.includes('酒店') || item.name.includes('旅館') || item.name.includes('民宿') || item.name.includes('商旅') || item.name.includes('行館') || item.name.includes('渡假村') || item.name.includes('Hotel') || item.name.includes('Resort');
                                
                                let finalLocation = new google.maps.LatLng(item.lat, item.lng);
                                if (isDashboard) {
                                    await new Promise((resolve) => { geocoder.geocode({ address: item.name }, (results, status) => { if (status === 'OK' && results[0]) finalLocation = results[0].geometry.location; resolve(); }); });
                                }
                                let modeStr = (data.travel_mode === 'TWO_WHEELER') ? '機車' : '開車';
                                let travel = (index === 0) ? '起點' : (item.travel_time ? `${modeStr} ${item.travel_time}` : '');
                                let stay = item.stay_time ? `停留 ${item.stay_time}` : '';
                                let cost = item.cost_estimate ? `${item.cost_estimate}` : '';
                                let reason = item.reason ? `${item.reason}` : '';
                                
                                let aiDesc = [travel, stay, cost, reason].filter(Boolean).join(' ｜ ');

                                // 💡 新增：動態賦予標籤，讓 isLodging 可以抓到
                                let placeTypes = ['tourist_attraction']; // 預設為景點
                                if (isDashboard) placeTypes = ['premise'];
                                if (isHotel) placeTypes = ['lodging', 'tourist_attraction']; // 給予住宿標籤

                                newPoints.push({
                                    id: Date.now() + index + (dayNum * 1000), 
                                    name: item.name, 
                                    location: finalLocation, 
                                    ai_description: aiDesc,
                                    note: "", 
                                    rating: 5, 
                                    types: placeTypes, 
                                    reviews: [], 
                                    isLocked: false,
                                    // 💡 關鍵修正：把 AI 傳過來的停車模式存進去！
                                    parking_mode: item.parking_mode || null
                                });
                            }
                            itineraryData[dayNum] = newPoints;
                        }
                        dayCount = Math.max(dayCount, highestDay);
                        for(let i = 1; i <= dayCount; i++) if(!itineraryData[i]) itineraryData[i] = [];
                        
                        if (data.days.length > 0) { currentDay = parseInt(data.days[data.days.length - 1].day) || currentDay; }
                        renderDayTabs(); updateUI(); refreshMarkersOnly();
                    }
                    
                    document.getElementById('ai-summary-container').classList.remove('hidden');
                    document.getElementById('ai-itinerary-summary').innerHTML = `
                        <div class="p-4 bg-slate-800 rounded-2xl shadow-sm">
                            <div class="flex items-center gap-2 mb-2 text-indigo-300 font-bold text-[12px]"><i class="bi bi-stars"></i> 規劃總結</div>
                            <div class="text-[13px] text-white leading-relaxed font-medium">${data.ai_message}</div>
                        </div>`;
                    
                    document.getElementById('ai-suggestion-box').classList.add('hidden');
                    document.getElementById('ai-map-instruction-content').innerHTML = '';
                    document.getElementById('ai-distance-suggestion').innerHTML = '';
                    document.getElementById('ai-time-suggestion').innerHTML = '';
                } else {
                    alert("❌ 規劃失敗：" + (data.message || "請檢查系統設定"));
                }
            } catch (error) {
                alert("🚨 連線異常，請確認伺服器運作中");
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-70', 'cursor-not-allowed');
                icon.className = "bi bi-magic";
                btnText.innerText = "生成建議路線";
            }
        }

        function moveItem(index, direction) { const currentItinerary = itineraryData[currentDay]; const target = index + direction; if (target < 0 || target >= currentItinerary.length) return; const temp = currentItinerary[index]; currentItinerary[index] = currentItinerary[target]; currentItinerary[target] = temp; updateUI(); refreshMarkersOnly(); if (routeLines.length > 0) calculateRoute(); }
        function removeItem(id) { itineraryData[currentDay] = itineraryData[currentDay].filter(p => p.id !== id); updateUI(); refreshMarkersOnly(); if (itineraryData[currentDay].length >= 2) { calculateRoute(); } else { clearAllRoutes(); } }
        function toggleTraffic() { if (trafficLayer.getMap()) { trafficLayer.setMap(null); } else { trafficLayer.setMap(map); } }
        function updateTravelMode(mode) { currentMode = mode; document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active')); document.getElementById(`btn-${mode}`).classList.add('active'); if (itineraryData[currentDay].length >= 2) calculateRoute(); }
        function toggleLeg(idx, checked) { if (checked) visibleLegs.add(idx); else visibleLegs.delete(idx); const totalLegs = itineraryData[currentDay].length - 1; const allChecked = visibleLegs.size === totalLegs && totalLegs > 0; document.getElementById('toggle-all-routes').checked = allChecked; updateRouteVisibility(); renderAISuggestions(); }
        function toggleAllRoutes(checked) { document.querySelectorAll('#route-toggle-list input[type="checkbox"]').forEach(cb => cb.checked = checked); if (checked) for(let i=0; i < itineraryData[currentDay].length - 1; i++) visibleLegs.add(i); else visibleLegs.clear(); updateRouteVisibility(); renderAISuggestions(); }
        function updateRouteToggleUI() { const currentItinerary = itineraryData[currentDay]; const list = document.getElementById('route-toggle-list'); list.innerHTML = ''; for(let i=0; i < currentItinerary.length - 1; i++) { list.innerHTML += `<label class="flex items-center gap-2 cursor-pointer bg-slate-50 p-2 rounded-lg border border-slate-100 hover:bg-slate-100 transition"><input type="checkbox" checked onchange="toggleLeg(${i}, this.checked)" class="cursor-pointer accent-slate-800 rounded-sm"><span class="font-medium truncate" style="color:${colorPalette[i % colorPalette.length]}">段落 ${i+1}➔${i+2}</span></label>`; } }
        function updateRouteVisibility() { routeLines.forEach(l => l.setMap(visibleLegs.has(l.legIndex) ? map : null)); routeLabels.forEach(l => l.setMap(visibleLegs.has(l.legIndex) ? map : null)); }
        window.onload = initMap;
    </script>
</body>
</html>