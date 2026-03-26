<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI 智慧旅遊規劃系統 - 多天數存檔版</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvAAdWjnxCHy6kfojvWq4iO4wKHOl14eY&libraries=places,marker&v=beta&language=zh-TW&callback=initMap" async defer></script>

    <style>
        #map { height: 100vh; width: 100%; }
        body, html { height: 100%; margin: 0; padding: 0; overflow: hidden; }
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        #search-results-panel { max-height: 0; transition: max-height 0.3s ease-out; }
        #search-results-panel.active { max-height: 400px; }
        .move-btn { opacity: 0.4; transition: opacity 0.2s; }
        .group:hover .move-btn { opacity: 1; }
        
        .mode-btn { transition: all 0.2s ease; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 4px; }
        .mode-btn.active { background-color: #2563eb !important; color: white !important; font-weight: 800; border: 2px solid #1e40af; }
        
        .day-tab { white-space: nowrap; transition: all 0.2s; cursor: pointer; }
        .day-tab.active { background-color: #2563eb; color: white; border-color: #1e40af; box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.3); }
        .day-tab:hover:not(.active) { background-color: #e2e8f0; }

        .route-label { 
            background: rgba(255, 255, 255, 1.0); padding: 5px 12px; border-radius: 16px; border: 2.2px solid #7c3aed; 
            color: #7c3aed; font-size: 11px; font-weight: 800; box-shadow: 0 4px 12px rgba(0,0,0,0.2); 
            white-space: nowrap; pointer-events: none; display: flex; align-items: center; gap: 6px; z-index: 500;
        }
        .diff-label {
            background: rgba(255, 255, 255, 0.95); padding: 3px 8px; border-radius: 10px;
            font-size: 10px; font-weight: bold; box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            pointer-events: auto; cursor: pointer; white-space: nowrap; z-index: 150;
            transition: all 0.2s ease; border: 1.5px solid transparent;
        }
        .diff-faster { color: #059669; border-color: #059669; }
        .diff-slower { color: #64748b; border-color: #94a3b8; }
        .transit-step { border-left: 2.5px dashed #cbd5e1; margin-left: 10px; padding-left: 15px; position: relative; margin-bottom: 12px; }
        .transit-step::before { content: ''; position: absolute; left: -6.5px; top: 0; width: 10px; height: 10px; background: #94a3b8; border-radius: 50%; border: 2px solid white; }

        @keyframes bounce-short { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-5px); } }
        .animate-bounce-short { animation: bounce-short 2s ease-in-out infinite; }
    </style>
</head>
<body class="bg-slate-50 font-sans">

    <div class="flex h-screen w-full overflow-hidden">
        <div class="w-80 md:w-[420px] bg-white shadow-2xl z-20 flex flex-col flex-shrink-0 border-r border-slate-200">
            
            <div class="p-5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex justify-between items-center shadow-md relative z-30">
                <a href="/" class="hover:scale-105 transition-transform block">
                    <h1 class="text-xl font-bold flex items-center gap-2"><span>🚀</span> AI趣玩 </h1>
                    <p class="text-blue-100 text-[10px] mt-0.5">Multi-Day Smart Planning</p>
                </a>
                
                <div class="flex items-center gap-3">
                    @if (Route::has('login'))
                        @auth

                            <a href="{{ route('feed.index') }}" class="text-[12px] font-bold text-blue-100 hover:text-white transition flex items-center gap-1 mr-3 border-r border-blue-400/50 pr-3">
                                <span>🌍</span> 社群動態
                            </a>
                            

                            <div class="flex flex-col items-end">
                                <a href="{{ route('dashboard') }}" class="text-[11px] text-blue-100 font-bold hover:text-white transition flex items-center gap-1">
                                    <span>⚙️</span> 控制台
                                </a>
                                <form method="POST" action="{{ route('logout') }}" class="mt-0.5">
                                    @csrf
                                    <button type="submit" class="text-[10px] text-blue-200 hover:text-white transition underline">登出帳號</button>
                                </form>
                            </div>
                        @else
                            <div class="flex items-center gap-2">
                                <a href="{{ route('login') }}" class="text-[12px] font-bold text-blue-100 hover:text-white transition">登入</a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="bg-white text-indigo-600 hover:bg-blue-50 px-2.5 py-1 rounded-md text-[11px] font-black shadow-sm transition">註冊</a>
                                @endif
                            </div>
                        @endauth
                    @endif

                    <button onclick="saveFullTrip()" class="bg-indigo-500 hover:bg-indigo-400 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md transition flex items-center gap-1 border border-indigo-400 ml-1">
                        <span>💾</span> 存檔
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                
                <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar" id="day-tabs-container"></div>

                <div class="space-y-3 relative">
                    <label class="text-xs font-bold text-slate-400 uppercase tracking-widest">🔍 探索景點或地址</label>
                    <div class="flex gap-1">
                        <input type="text" id="pac-input" class="flex-1 border border-slate-200 rounded-lg px-4 py-2.5 text-sm focus:ring-2 focus:ring-blue-500 outline-none transition" placeholder="輸入名稱或地址後按 Enter">
                        <button onclick="searchPlace()" class="bg-blue-600 text-white px-5 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition shadow-md">搜尋</button>
                    </div>
                    <div id="search-results-panel" class="hidden absolute left-0 right-0 bg-white border border-slate-200 rounded-lg shadow-xl z-30 overflow-y-auto custom-scrollbar"></div>
                </div>

                <div class="grid grid-cols-5 gap-1.5">
                    <button onclick="updateTravelMode('DRIVING')" class="mode-btn p-3 bg-slate-100 text-slate-600 rounded-xl text-[13px] font-bold active" id="btn-DRIVING"><span>🚗</span><span>開車</span></button>
                    <button onclick="updateTravelMode('TWO_WHEELER')" class="mode-btn p-3 bg-slate-100 text-slate-600 rounded-xl text-[13px] font-bold" id="btn-TWO_WHEELER"><span>🛵</span><span>騎車</span></button>
                    <button onclick="updateTravelMode('TRANSIT')" class="mode-btn p-3 bg-slate-100 text-slate-600 rounded-xl text-[13px] font-bold" id="btn-TRANSIT"><span>🚌</span><span>轉乘</span></button>
                    <button onclick="updateTravelMode('BICYCLING')" class="mode-btn p-3 bg-slate-100 text-slate-600 rounded-xl text-[13px] font-bold" id="btn-BICYCLING"><span>🚲</span><span>單車</span></button>
                    <button onclick="updateTravelMode('WALKING')" class="mode-btn p-3 bg-slate-100 text-slate-600 rounded-xl text-[13px] font-bold" id="btn-WALKING"><span>🚶</span><span>步行</span></button>
                </div>

                <div class="bg-indigo-50 p-4 rounded-2xl border border-indigo-100 shadow-sm space-y-3 my-4 animate-in fade-in">
                    <label class="text-[11px] font-bold text-indigo-800 uppercase tracking-widest flex items-center gap-2">
                        <span class="text-base">🤖</span> AI 智慧行程助手 (含停車/雨天備案)
                    </label>
                    <textarea id="ai-chat-prompt" rows="2" 
                        class="w-full border border-indigo-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-indigo-500 outline-none transition resize-none custom-scrollbar" 
                        placeholder="試著輸入：我明天有 5 小時，想在台中看海＋吃甜點..."></textarea>
                    <button onclick="askAIForItinerary()" id="ai-gen-btn" 
                        class="w-full bg-indigo-600 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-indigo-700 transition shadow-md flex justify-center items-center gap-2">
                        <span id="ai-btn-icon">✨</span> <span id="ai-btn-text">生成建議行程</span>
                    </button>
                </div>
                
                <div id="route-toggles" class="hidden bg-slate-50 p-4 rounded-2xl border border-slate-200 space-y-3 animate-in fade-in">
                    <h3 class="font-bold text-slate-700 text-sm flex justify-between items-center">
                        <span>🗺️ 路徑顯示控制</span>
                        <label class="flex items-center gap-1 text-[11px] text-blue-600 cursor-pointer hover:text-blue-800 transition">
                            <input type="checkbox" id="toggle-all-routes" checked onchange="toggleAllRoutes(this.checked)" class="accent-blue-600"> 全選
                        </label>
                    </h3>
                    <div id="route-toggle-list" class="grid grid-cols-2 gap-3 text-[12px]"></div>
                </div>

                <div class="space-y-4">
                    <div class="flex justify-between items-center border-b pb-3">
                        <h2 class="font-bold text-slate-700 text-base flex items-center gap-2">
                            <span id="current-day-label">📍 Day 1 行程</span>
                        </h2>
                        <span class="text-[11px] bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-bold" id="point-count">0 個地點</span>
                    </div>
                    
                    <button onclick="smartOptimizeRoute()" id="optimize-btn" class="hidden w-full mb-1 py-2 bg-gradient-to-r from-purple-500 to-indigo-600 text-white font-bold rounded-xl shadow-md hover:shadow-lg transform hover:-translate-y-0.5 transition-all text-[13px] flex justify-center items-center gap-2">
                        <span>✨</span> 智能一鍵順路最佳化 (保留鎖定)
                    </button>

                    <div id="itinerary-list" class="space-y-3 min-h-[100px]"></div>
                    
                    <button onclick="calculateRoute()" id="route-btn" class="hidden w-full bg-emerald-600 text-white py-4 rounded-2xl font-extrabold text-base hover:bg-emerald-700 shadow-lg transition transform hover:scale-[1.02]">
                        計算 Day <span id="btn-day-num">1</span> 路徑
                    </button>
                </div>

                <div id="ai-summary-container" class="hidden animate-in fade-in mt-4">
                    <div id="ai-itinerary-summary"></div>
                </div>

                <div id="ai-suggestion-box" class="hidden p-0 animate-in fade-in mt-4">
                    <div id="ai-time-suggestion"></div>
                    
                    <div class="p-5 bg-white rounded-2xl border border-slate-200 shadow-sm mt-3">
                        <h3 class="text-slate-800 font-bold text-[13px] mb-3 flex items-center gap-1">🤖 AI 即時導引與建議</h3>
                        <div id="ai-map-instruction" class="text-[12px] text-slate-600 leading-relaxed space-y-4">
                            <div id="ai-map-instruction-content"></div>
                            <div id="ai-distance-suggestion"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="flex-1 relative">
            <div id="map"></div>
            <button onclick="toggleTraffic()" class="absolute top-4 right-14 bg-white px-3 py-2 rounded-lg shadow-md z-10 text-[13px] font-bold hover:bg-slate-50 transition border border-slate-200">🚦 即時路況</button>
        </div>
    </div>

    <script>
        const isLoggedIn = {{ Auth::check() ? 'true' : 'false' }};
        
        // 💡 抓蟲手術：實作「切換帳號/登入登出」自動清空草稿的機制
        const currentUserId = "{{ Auth::check() ? Auth::id() : 'guest' }}"; // 現在是誰？
        const lastUserId = sessionStorage.getItem('last_user_state');         // 剛剛是誰？

        // 如果發現狀態改變了（例如從 guest 變成登入，或從登入變成 guest）
        if (lastUserId !== null && lastUserId !== currentUserId) {
            sessionStorage.removeItem('trip_ai_memory');
            sessionStorage.removeItem('trip_itinerary_memory');
            console.log('🔄 登入狀態改變，已自動清空所有地圖草稿');
        }
        
        // 更新狀態紀錄，等下次重新整理時用來比對
        sessionStorage.setItem('last_user_state', currentUserId);

        const loadedTripJson = {!! json_encode($loadedTrip ? $loadedTrip->itinerary_data : null) !!};
        const loadedTripTitle = {!! json_encode($loadedTrip ? $loadedTrip->title : null) !!};
        // 💡 手術刀 1：接收後端傳來的行程專屬記憶
        const loadedTripChatHistory = {!! json_encode($loadedTrip ? $loadedTrip->chat_history : null) !!};

        let map, service, geocoder, directionsService, trafficLayer;
        let itineraryData = { 1: [] };
        let currentDay = 1;
        let dayCount = 1;
        
        let aiChatHistory = {}; 
        if (loadedTripChatHistory) {
            aiChatHistory = loadedTripChatHistory; // 從控制台載入的專屬記憶
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
            aiChatHistory = {}; // 清空變數
            
            // 偷偷把網址列的 ?new=1 擦掉，這樣如果不小心按 F5 才不會又被清空一次
            window.history.replaceState({}, document.title, "/");
        }

        let markers = [], routeLines = [], routeLabels = [];
        let currentMode = 'DRIVING', visibleLegs = new Set(), selectedRoutesMap = {}; 
        let lastShownDetailId = null;
        let tempOptimizedItinerary = null; 
        let currentAnalysisId = 0; 

        const colorPalette = ["#7c3aed", "#ec4899", "#f59e0b", "#10b981", "#3b82f6", "#ef4444", "#06b6d4"];

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
                    label.innerHTML = `📍 Day 1 行程 <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md ml-2 border border-indigo-200">📂 ${loadedTripTitle}</span>`;
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
                            // 將文字格式的座標轉回 Google Maps 物件
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

            const defaultTitle = loadedTripTitle ? loadedTripTitle + " (修改版)" : "我的超讚旅行";
            const title = prompt("請為這趟旅程取個名字：", defaultTitle);
            if (!title) return;

            const payload = {
                title: title,
                itinerary_data: itineraryData,
                chat_history: aiChatHistory // 💡 手術刀 3：把對話記憶一起打包送給後端
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
                    sessionStorage.removeItem('trip_ai_memory'); // 💡 存檔成功後，清空瀏覽器暫存
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
                btn.className = `day-tab py-2 pl-4 ${paddingRight} rounded-xl text-sm font-bold border border-slate-200 transition-all ${currentDay === i ? 'active' : 'bg-white text-slate-600'}`;
                btn.innerText = `Day ${i}`; 
                btn.onclick = () => switchDay(i); 
                wrapper.appendChild(btn);

                if (dayCount > 1) {
                    const delBtn = document.createElement('button');
                    delBtn.innerHTML = "✕";
                    delBtn.className = `absolute right-2.5 top-1/2 -translate-y-1/2 text-[13px] font-extrabold transition-colors ${currentDay === i ? 'text-blue-300 hover:text-white' : 'text-slate-300 hover:text-red-500'}`;
                    delBtn.onclick = (e) => { e.stopPropagation(); removeDay(i); };
                    wrapper.appendChild(delBtn);
                }

                container.appendChild(wrapper);
            }
            const addBtn = document.createElement('button'); addBtn.className = "px-3 py-2 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 font-bold border border-slate-200 transition shrink-0";
            addBtn.innerHTML = "+"; addBtn.onclick = addNewDay; container.appendChild(addBtn);
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
            
            // 💡 手術刀：切換天數時隱藏提示
            document.getElementById('ai-suggestion-box').classList.add('hidden'); 
            document.getElementById('ai-summary-container').classList.add('hidden');
            
            document.getElementById('current-day-label').innerText = `📍 Day ${currentDay} 行程`; 
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
            
            // 💡 手術刀：切換天數時隱藏提示
            document.getElementById('ai-suggestion-box').classList.add('hidden'); 
            document.getElementById('ai-summary-container').classList.add('hidden');
            
            let titleTag = (loadedTripTitle && currentDay === 1) ? `<span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-md ml-2 border border-indigo-200">📂 ${loadedTripTitle}</span>` : '';
            document.getElementById('current-day-label').innerHTML = `📍 Day ${currentDay} 行程 ${titleTag}`; 
            
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
            const panel = document.getElementById("search-results-panel"); panel.innerHTML = `<div class="p-4 text-xs italic">🔍 搜尋中...</div>`; panel.classList.remove("hidden"); panel.classList.add("active");
            service.textSearch({ query, location: map.getCenter(), radius: '5000', language: 'zh-TW' }, (results, status) => { if (status === 'OK') renderSearchResults(results); else panel.innerHTML = `<div class="p-4 text-xs text-red-500 font-bold">❌ 找不到地點。</div>`; });
        }

        function renderSearchResults(results) {
            const panel = document.getElementById("search-results-panel"); panel.innerHTML = "";
            results.slice(0, 5).forEach(place => {
                const div = document.createElement("div"); div.className = "p-4 border-b border-slate-100 hover:bg-blue-50 cursor-pointer flex items-start gap-3 transition";
                div.innerHTML = `<div class="mt-1 text-xl">📍</div><div class="flex-1 overflow-hidden"><div class="text-sm font-bold truncate">${place.name}</div><div class="text-[11px] text-slate-400 truncate">${place.formatted_address}</div></div>`;
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
            // 💡 手術刀：清除路徑時，千萬不要隱藏黃色提醒框 (ai-summary-container)！
            document.getElementById('route-toggles').classList.add('hidden'); 
            visibleLegs.clear(); 
            selectedRoutesMap = {}; 
        }

        function refreshMarkersOnly() {
            markers.forEach(m => m.setMap(null)); markers = []; const counts = {}; const currentItinerary = itineraryData[currentDay] || [];
            currentItinerary.forEach((p, index) => {
                const key = `${p.location.lat().toFixed(6)},${p.location.lng().toFixed(6)}`; let lat = p.location.lat(), lng = p.location.lng();
                if (counts[key]) { lat += (counts[key] * 0.00022); lng += (counts[key] * 0.00022); counts[key]++; } else { counts[key] = 1; }
                const glyph = document.createElement('div'); glyph.className = 'bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-base shadow-lg border-2 border-white'; glyph.innerText = (index + 1).toString();
                markers.push(new google.maps.marker.AdvancedMarkerElement({ map, position: {lat, lng}, content: glyph }));
            });
        }

        async function calculateRoute() {
            const currentItinerary = itineraryData[currentDay] || []; if (currentItinerary.length < 2) return;
            clearAllRoutes(); document.getElementById('route-toggles').classList.remove('hidden');
            
            const aiBox = document.getElementById('ai-suggestion-box');
            aiBox.classList.remove('hidden'); 
            
            document.getElementById('ai-map-instruction-content').innerHTML = `<div class="text-[12px] text-slate-500 mb-2">正在計算 Day ${currentDay} 最佳路徑...</div>`;
            document.getElementById('ai-distance-suggestion').innerHTML = '';
            document.getElementById('ai-time-suggestion').innerHTML = '';

            for(let i=0; i < currentItinerary.length - 1; i++) { visibleLegs.add(i); selectedRoutesMap[i] = { index: 0, result: null }; }
            updateRouteToggleUI(); 
            for (let i = 0; i < currentItinerary.length - 1; i++) {
                const results = await requestRouteByMode(currentItinerary[i].location, currentItinerary[i+1].location, currentMode);
                if (results && results.routes) { selectedRoutesMap[i].result = results; drawLeg(results.routes, i); } else { 
                    document.getElementById('ai-map-instruction-content').innerHTML = `
                        <div class="p-3 bg-red-50 rounded-xl border border-red-100 mb-2">
                            <span class="text-red-600 font-bold text-[13px] flex items-center gap-1">
                                ❌ 第 ${i+1} 段路線計算失敗
                            </span>
                            <div class="text-[11px] text-red-500 mt-2 space-y-1">
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
                
                box.innerHTML = `<div class="mt-3 text-indigo-500 text-[12px] animate-pulse flex items-center gap-1"><span>🧠</span> AI 正在評估最佳造訪時間...</div>`;

                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        prompt: promptValue, 
                        mode: currentMode,
                        history: [], 
                        all_itineraries: {} 
                    })
                });

                if (myAnalysisId !== currentAnalysisId) return; 

                const data = await response.json();

                if (response.ok && data.status === 'success') {
                    box.innerHTML = `<div class="mt-3 p-3 bg-indigo-50 text-indigo-800 text-[12px] font-bold rounded-lg border border-indigo-200 shadow-sm leading-relaxed">
                        <div class="flex items-center gap-1 mb-1 text-[13px]"><span>🕒</span> 日夜時間提醒：</div>
                        ${data.ai_message}
                    </div>`;
                } else {
                    box.innerHTML = '';
                }
            } catch (error) {
                console.error(error);
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
            btn.classList.add('opacity-70');
            btn.innerHTML = "🧠 系統深度思考與重排中...";

            try {
                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        prompt: promptValue, 
                        mode: currentMode,
                        history: [], 
                        all_itineraries: {} 
                    })
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
                            
                            // 💡 手術刀：完美復原時間規劃邏輯，確保有值才顯示
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
                    
                    alert("✨ 智能一鍵順路最佳化完成！");
                } else {
                    alert("❌ 最佳化失敗：" + (data.message || "請檢查 API 設定"));
                }
            } catch (error) {
                alert("🚨 連線異常，請確認 Laravel 伺服器運作中");
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-70');
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
                if (ratio > 1.25) return '#ef4444'; if (ratio > 1.0) return '#f59e0b'; return '#10b981';                   
            } return defaultColor;
        }

        function drawLeg(routes, legIndex) {
            const legColor = colorPalette[legIndex % colorPalette.length];
            routes.forEach((route, rIdx) => {
                const isSel = (rIdx === selectedRoutesMap[legIndex].index); const off = (legIndex * 0.00015) + (rIdx * 0.00005);
                const pathCoords = route.overview_path.map(c => ({ lat: c.lat() + off, lng: c.lng() + off }));
                const strokeColor = isSel ? getTrafficColor(route, legColor) : legColor;
                const lineOptions = { path: pathCoords, strokeColor: strokeColor, strokeOpacity: isSel ? 1.0 : 0.5, strokeWeight: isSel ? 9 : 5, zIndex: isSel ? 100 : 10, map: map };
                if (isSel) { lineOptions.icons = [{ icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 4, strokeWeight: 2, fillColor: '#FFFFFF', fillOpacity: 1, strokeColor: '#000000' }, offset: '95%' }]; }
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
                let timeText = curLeg.duration.text.replace("mins", "分"); let colorClass = "text-indigo-600";
                if (curLeg.duration_in_traffic) {
                    timeText = curLeg.duration_in_traffic.text.replace("mins", "分"); const ratio = curLeg.duration_in_traffic.value / curLeg.duration.value;
                    if (ratio > 1.25) colorClass = "text-red-600 font-black"; else if (ratio > 1.0) colorClass = "text-amber-600 font-black"; else colorClass = "text-emerald-600 font-black";
                }
                marker.content.className = 'route-label'; marker.content.innerHTML = `<span>${legIndex+1}➔${legIndex+2}</span><span class="text-slate-300">|</span><span>${curLeg.distance.text}</span><span class="text-slate-300">|</span><span class="${colorClass}">${timeText}</span>`; marker.zIndex = 500;
            } else {
                const diff = Math.round((curLeg.duration.value - priVal) / 60); marker.content.className = `diff-label ${diff < 0 ? 'diff-faster' : 'diff-slower'}`; marker.content.innerText = diff < 0 ? `省 ${Math.abs(diff)} 分` : (diff > 0 ? `慢 ${diff} 分` : "同時間");
                marker.zIndex = 200; marker.content.onclick = (e) => { e.stopPropagation(); selectedRoutesMap[legIndex].index = marker.routeIndex; refreshGraphics(); renderAISuggestions(); };
            }
        }

        function refreshGraphics() { 
            routeLines.forEach(l => { 
                const isSel = (l.routeIndex === selectedRoutesMap[l.legIndex].index); const strokeColor = isSel ? getTrafficColor(l.routeData, l.originalColor) : l.originalColor;
                const newOptions = { strokeColor: strokeColor, strokeOpacity: isSel ? 1.0 : 0.5, strokeWeight: isSel ? 9 : 5, zIndex: isSel ? 100 : 10 };
                if (isSel) { newOptions.icons = [{ icon: { path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW, scale: 4, strokeWeight: 2, fillColor: '#FFFFFF', fillOpacity: 1, strokeColor: '#000000' }, offset: '95%' }]; } else { newOptions.icons = []; } l.setOptions(newOptions);
            }); routeLabels.forEach(m => updateSingleLabel(m, m.legIndex)); updateRouteVisibility(); 
        }

        function checkOptimalRouteSuggestion() {
            const currentItinerary = itineraryData[currentDay] || []; 
            if (currentItinerary.length < 3 || currentMode === 'TRANSIT') return;

            let optMode = (currentMode === 'TWO_WHEELER') ? 'TWO_WHEELER' : currentMode;
            let request = { 
                origin: currentItinerary[0].location, 
                destination: currentItinerary[currentItinerary.length - 1].location, 
                waypoints: currentItinerary.slice(1, -1).map(p => ({ location: p.location, stopover: true })), 
                optimizeWaypoints: true, 
                travelMode: google.maps.TravelMode[optMode] || optMode 
            };
            
            if (currentMode === 'TWO_WHEELER') { 
                request.travelMode = google.maps.TravelMode.DRIVING; 
                request.avoidHighways = true; 
                request.avoidTolls = true; 
            }
            
            directionsService.route(request, (res, status) => {
                if (status === 'OK') {
                    const opt = res.routes[0].waypoint_order; 
                    let swap = "";
                    let hasLocks = currentItinerary.some(item => item.isLocked);
                    
                    for(let i=0; i<opt.length; i++) {
                        if(opt[i] !== i) {
                            let originalIndex = i + 1;
                            let targetIndex = opt[i] + 1;

                            if (currentItinerary[originalIndex].isLocked || currentItinerary[targetIndex].isLocked) {
                                continue; 
                            }
                            swap = `將 <span class="text-red-600 font-bold">第 ${originalIndex + 1} 站</span> 與 <span class="text-red-600 font-bold">第 ${targetIndex + 1} 站</span> 互換更省時！`; 
                            break; 
                        }
                    }
                    
                    let suggestionHTML = "";

                    if (swap) {
                        suggestionHTML += `
                            <div class="mt-3 p-3 bg-blue-50 text-blue-800 rounded-lg border border-blue-200 leading-relaxed shadow-sm">
                                <span class="font-bold">💡 距離建議：</span>${swap} <br>
                                <span class="text-blue-500 text-[11px]">(可點擊上方 ✨智能最佳化 按鈕一鍵重排)</span>
                            </div>
                        `; 
                    } else if (hasLocks) {
                        suggestionHTML += `<div class="mt-3 p-2 bg-emerald-50 text-emerald-700 font-bold rounded-lg border border-emerald-200">🔒 距離建議：在您鎖定的條件下，順序已達最佳化！</div>`;
                    } else {
                        suggestionHTML += `<div class="mt-3 p-2 bg-emerald-50 text-emerald-700 font-bold rounded-lg border border-emerald-200">✅ 距離建議：目前的行程順序已經是最順路的囉！</div>`;
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
                    html += `<div class="mb-4 border-b border-indigo-100 pb-3">📍 第 ${i + 1} 段詳情：${currentItinerary[i].name} ➔ ${currentItinerary[i+1].name}`;
                    leg.steps.forEach(step => {
                        const dur = step.duration.text.replace("mins", "分鐘");
                        if (step.travel_mode === 'TRANSIT') { html += `<div class="transit-step"><span class="text-blue-600 font-extrabold text-[13px]">🚌 搭乘 ${step.transit.line.short_name || step.transit.line.name}</span><br><span class="text-[11px] text-slate-500">於 ${step.transit.departure_stop.name} 上車 (約 ${dur})</span></div>`; } else { html += `<div class="transit-step"><span class="text-slate-600 text-[12px]">🚶 ${step.instructions.replace(/Walk to /i, "步行至 ")}</span></div>`; }
                    }); html += `</div>`;
                } box.innerHTML = html || "✅ 已規劃最佳方案。"; 
            } else { 
                box.innerHTML = `<span class='flex items-center gap-1'>🗺️ 點擊地圖上的淺色線條可切換不同行車路線。</span>`; 
            }
        }

        function toggleDetail(index) { 
            const currentItinerary = itineraryData[currentDay] || []; 
            const targetContainer = document.getElementById(`item-detail-${index}`);
            
            if (lastShownDetailId === currentItinerary[index].id && !targetContainer.classList.contains('hidden')) { 
                targetContainer.classList.add('hidden'); 
                lastShownDetailId = null; 
            } else { 
                for(let i=0; i<currentItinerary.length; i++) {
                    const el = document.getElementById(`item-detail-${i}`);
                    if(el) el.classList.add('hidden');
                }
                
                targetContainer.classList.remove('hidden'); 
                lastShownDetailId = currentItinerary[index].id; 
                showPlaceDetail(currentItinerary[index], targetContainer); 
            } 
        }

        async function showPlaceDetail(point, container) {
            const isDashboardOrDeparture = point.name.includes("出發") || point.name.includes("回家") || point.name.includes("巷") || point.name.includes("號") || point.name.includes("住家") || point.name.includes("返程");

            if (!point.photo && !point.place_id && !isDashboardOrDeparture) {
                container.innerHTML = `<div class="py-6 text-center text-blue-500 text-[12px] font-bold flex flex-col items-center gap-2">
                    <span class="animate-spin text-xl">🔄</span> 載入真實照片與評價...
                </div>`;
                
                await new Promise((resolve) => {
                    service.textSearch({ query: point.name, location: point.location, radius: '1000' }, (results, status) => {
                        if (status === 'OK' && results[0]) {
                            service.getDetails({ placeId: results[0].place_id, fields: ["photos", "reviews", "types", "rating", "place_id"] }, (place, dStatus) => {
                                if (dStatus === 'OK') {
                                    point.place_id = place.place_id; 
                                    point.photo = place.photos ? place.photos[0].getUrl({ maxWidth: 400 }) : null;
                                    point.reviews = place.reviews || [];
                                    if(place.types) point.types = place.types;
                                }
                                resolve();
                            });
                        } else { resolve(); }
                    });
                });
            }

            const hour = new Date().getHours(); const types = point.types || []; let cat = "景點", advice = "環境穩定舒適。";
            
            if (types.some(t => ['school', 'university'].includes(t))) { cat = "🏫 教育設施"; advice = "⚠️ 內部可能不開放參訪。非教職員請注意。"; } 
            else if (types.some(t => ['restaurant', 'cafe', 'food'].includes(t))) { cat = "🍴 餐飲場所"; if ((hour >= 11 && hour <= 13) || (hour >= 17 && hour <= 19)) advice = "🍴 <b>正值用餐尖峰</b>，建議提早訂位。"; } 
            else if (types.includes('premise') || types.includes('street_address') || types.length === 0 || isDashboardOrDeparture) { cat = "🏠 特殊地點"; advice = "🤫 <b>可能是住家或私人區域</b>。若為住宅請保持安靜。"; }
            
            let revHtml = ""; if (point.reviews && point.reviews.length > 0) {
                let p = [], c = []; point.reviews.forEach(r => { if (r.rating >= 4 && p.length < 2) p.push(r.text.substring(0, 50)); if (r.rating <= 3 && c.length < 2) c.push(r.text.substring(0, 50)); });
                revHtml = `<div class="mt-3 grid grid-cols-2 gap-2"><div class="bg-green-50 p-2.5 rounded-xl border border-green-100"><p class="text-green-700 font-extrabold text-[12px] mb-1 flex items-center gap-1">✅ 讚點</p><ul class="text-[11px] text-green-800 space-y-1">${p.map(x=>`<li class="truncate">• ${x}</li>`).join('') || '<li>整體環境整潔</li>'}</ul></div><div class="bg-red-50 p-2.5 rounded-xl border border-red-100"><p class="text-red-700 font-extrabold text-[12px] mb-1 flex items-center gap-1">⚠️ 注意</p><ul class="text-[11px] text-red-800 space-y-1">${c.map(x=>`<li class="truncate">• ${x}</li>`).join('') || '<li>無特殊差評</li>'}</ul></div></div>`;
            } 
            
            container.innerHTML = `
                <div class="mb-2">
                    <span class="bg-blue-50 text-blue-600 px-2.5 py-1 rounded-md text-[10px] border border-blue-100 font-extrabold uppercase tracking-tighter">${cat}</span>
                </div>
                ${point.photo ? `<img src="${point.photo}" class="rounded-xl mb-3 w-full h-32 object-cover shadow-sm">` : ''}
                <div class="p-3 bg-slate-50 rounded-xl text-slate-600 text-[12px] flex gap-2 border border-slate-100 shadow-sm">
                    <div class="flex-shrink-0 text-sm">🕒</div>
                    <div class="flex-1 leading-relaxed"><span class="font-bold text-slate-700">AI 真實提醒：</span><span>${advice}</span></div>
                </div>
                ${revHtml}
            `;
        }

        function editNote(id) { const currentItinerary = itineraryData[currentDay]; const item = currentItinerary.find(p => p.id === id); if (item) { const newNote = prompt(`為「${item.name}」加上個人備註（例如：買伴手禮、必吃滷肉飯）：`, item.note || ""); if (newNote !== null) { item.note = newNote; updateUI(); } } }

        function toggleLock(index) {
            const currentItinerary = itineraryData[currentDay];
            if (!currentItinerary || !currentItinerary[index]) return;
            currentItinerary[index].isLocked = !currentItinerary[index].isLocked;
            updateUI(); 

            if (routeLines.length > 0) {
                calculateRoute(); 
            }
        }

        function updateUI() {
            sessionStorage.setItem('trip_itinerary_memory', JSON.stringify(itineraryData));
            const currentItinerary = itineraryData[currentDay] || []; 
            const list = document.getElementById('itinerary-list'); 
            document.getElementById('point-count').innerText = `${currentItinerary.length} 個地點`; 
            document.getElementById('route-btn').classList.toggle('hidden', currentItinerary.length < 2);
            document.getElementById('optimize-btn').classList.toggle('hidden', currentItinerary.length < 3);

            if (currentItinerary.length === 0) { list.innerHTML = `<div class="text-center text-slate-400 py-8 text-sm">📍 Day ${currentDay} 尚未新增地點</div>`; return; }
            
            list.innerHTML = currentItinerary.map((p, i) => {
                const hasParkingNote = p.ai_description && (p.ai_description.includes('停車') || p.ai_description.includes('找車位'));
                const isFree = p.ai_description && p.ai_description.includes('$0');

                return `
                <div class="bg-white border-l-4 ${p.isLocked ? 'border-red-500' : 'border-blue-500'} rounded-xl p-4 shadow-sm group animate-in slide-in-from-left duration-200">
                    <div class="flex justify-between items-start w-full">
                        <div class="flex-1 overflow-hidden">
                            <p class="text-[11px] ${p.isLocked ? 'text-red-500' : 'text-blue-500'} font-bold uppercase tracking-wider mb-1">站點 ${i+1}</p>
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-slate-800 text-[15px] truncate">${p.name}</p>
                                <button onclick="toggleDetail(${i})" class="text-blue-500 hover:text-blue-700 transition flex-shrink-0 bg-blue-50 p-1 rounded-full"><i class="bi bi-info-circle"></i></button>
                            </div>
                            
                            ${p.ai_description ? `
                                <div class="mt-1.5 bg-indigo-50 text-indigo-700 text-[11px] font-bold px-2.5 py-1.5 rounded-lg inline-block border border-indigo-100">
                                    <i class="bi bi-robot"></i> ${p.ai_description.split('｜').map(text => {
                                        if(text.includes('$')) return `<span class="${isFree ? 'text-emerald-600' : 'text-red-500'} font-black">${text}</span>`;
                                        return text;
                                    }).join(' ｜ ')}
                                </div>
                            ` : ''}

                            ${hasParkingNote ? `<div class="mt-1 flex items-center gap-1 text-blue-600 font-bold" style="font-size: 10px;"><i class="bi bi-p-square-fill"></i> 停車建議：已預留找位緩衝</div>` : ''}
                            ${p.note ? `<p class="text-[12px] text-emerald-600 mt-1.5 flex items-center gap-1 font-bold">📝 ${p.note}</p>` : ''}
                        </div>
                        <div class="flex items-center gap-1">
                            <button onclick="toggleLock(${i})" class="px-2 py-1 rounded text-[11px] font-bold ${p.isLocked ? 'bg-red-500 text-white shadow-inner' : 'bg-slate-100 text-slate-400 hover:bg-slate-200'}">
                                ${p.isLocked ? '🔒 已鎖定' : '🔓 鎖定'}
                            </button>
                            <button onclick="editNote(${p.id})" class="text-slate-300 hover:text-emerald-500 transition px-1"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                            <button onclick="moveItem(${i}, -1)" class="move-btn p-1 text-slate-300 hover:text-blue-600 ${i === 0 ? 'invisible' : ''}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                            <button onclick="moveItem(${i}, 1)" class="move-btn p-1 text-slate-300 hover:text-blue-600 ${i === currentItinerary.length-1 ? 'invisible' : ''}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                            <button onclick="removeItem(${p.id})" class="text-slate-200 hover:text-red-500 transition px-1 ml-1">✕</button>
                        </div>
                    </div>
                    <div id="item-detail-${i}" class="hidden w-full mt-3 pt-3 border-t border-slate-100 animate-in fade-in slide-in-from-top-2 duration-200"></div>
                </div>`;
            }).join('');
        }

        async function askAIForItinerary() {
            const promptValue = document.getElementById('ai-chat-prompt').value;
            if (!promptValue) return alert("請輸入您的想法喔！");

            const btn = document.getElementById('ai-gen-btn');
            const btnText = document.getElementById('ai-btn-text');
            
            btn.disabled = true;
            btn.classList.add('opacity-70');
            btnText.innerText = "正在精算停車與備案...";

            try {
                if (!aiChatHistory[currentDay]) aiChatHistory[currentDay] = [];

                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ 
                        prompt: promptValue, 
                        mode: currentMode,
                        history: aiChatHistory[currentDay],
                        all_itineraries: itineraryData,
                        current_day: currentDay // 💡 告訴後端我現在正在第幾天
                    })
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
                                let finalLocation = new google.maps.LatLng(item.lat, item.lng);
                                if (isDashboard) {
                                    await new Promise((resolve) => { geocoder.geocode({ address: item.name }, (results, status) => { if (status === 'OK' && results[0]) finalLocation = results[0].geometry.location; resolve(); }); });
                                }
                                let modeEmoji = (data.travel_mode === 'TWO_WHEELER') ? '🛵' : '🚗';
                                let travel = (index === 0) ? '📍 出發點' : (item.travel_time ? `${modeEmoji} ${item.travel_time}` : '');
                                
                                // 💡 手術刀：完美復原時間規劃邏輯，確保有值才顯示
                                let stay = item.stay_time ? `⏱️ ${item.stay_time}` : '';
                                let cost = item.cost_estimate ? `💰 ${item.cost_estimate}` : '';
                                let reason = item.reason ? `💡 ${item.reason}` : '';
                                
                                let aiDesc = [travel, stay, cost, reason].filter(Boolean).join(' ｜ ');

                                newPoints.push({
                                    id: Date.now() + index + (dayNum * 1000),
                                    name: item.name, location: finalLocation, ai_description: aiDesc,
                                    note: "", rating: 5, types: isDashboard ? ['premise'] : ['tourist_attraction'],
                                    reviews: [], isLocked: false
                                });
                            }
                            itineraryData[dayNum] = newPoints;
                        }
                        // 💡 修正最大天數計算，並移除強制跳回 Day 1 的 Bug
                        dayCount = Math.max(dayCount, highestDay);
                        for(let i = 1; i <= dayCount; i++) if(!itineraryData[i]) itineraryData[i] = [];
                        
                        // 讓畫面停留在 AI 剛剛幫你規劃的那一天 (通常就是你的 currentDay)
                        if (data.days.length > 0) {
                            currentDay = parseInt(data.days[data.days.length - 1].day) || currentDay;
                        }
                        renderDayTabs(); updateUI(); refreshMarkersOnly();
                    }
                    
                    // 💡 手術刀：讓黃色導遊提醒框獨立顯示
                    document.getElementById('ai-summary-container').classList.remove('hidden');
                    document.getElementById('ai-itinerary-summary').innerHTML = `
                        <div class="p-4 bg-amber-50 border-2 border-amber-200 rounded-2xl shadow-sm animate-bounce-short">
                            <div class="flex items-center gap-2 mb-2 text-amber-800 font-black"><i class="bi bi-chat-heart-fill"></i> AI 導遊貼心提醒：</div>
                            <div class="text-[13px] text-amber-900 leading-relaxed font-bold">${data.ai_message}</div>
                        </div>`;
                    
                    // 清空下方的藍色時間與地圖導航框，準備迎接新的計算
                    document.getElementById('ai-suggestion-box').classList.add('hidden');
                    document.getElementById('ai-map-instruction-content').innerHTML = '';
                    document.getElementById('ai-distance-suggestion').innerHTML = '';
                    document.getElementById('ai-time-suggestion').innerHTML = '';

                    alert("✨ AI 已完成停車與備案規劃！");
                } else {
                    alert("❌ AI 規劃失敗：" + (data.message || "請檢查 API 設定"));
                }
            } catch (error) {
                console.error(error);
                alert("🚨 連線異常，請確認 Laravel 伺服器運作中");
            } finally {
                btn.disabled = false;
                btn.classList.remove('opacity-70');
                btnText.innerText = "生成建議行程";
            }
        }

        function moveItem(index, direction) { const currentItinerary = itineraryData[currentDay]; const target = index + direction; if (target < 0 || target >= currentItinerary.length) return; const temp = currentItinerary[index]; currentItinerary[index] = currentItinerary[target]; currentItinerary[target] = temp; updateUI(); refreshMarkersOnly(); if (routeLines.length > 0) calculateRoute(); }
        function removeItem(id) { itineraryData[currentDay] = itineraryData[currentDay].filter(p => p.id !== id); updateUI(); refreshMarkersOnly(); if (itineraryData[currentDay].length >= 2) { calculateRoute(); } else { clearAllRoutes(); } }
        function toggleTraffic() { if (trafficLayer.getMap()) { trafficLayer.setMap(null); } else { trafficLayer.setMap(map); } }
        function updateTravelMode(mode) { currentMode = mode; document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active')); document.getElementById(`btn-${mode}`).classList.add('active'); if (itineraryData[currentDay].length >= 2) calculateRoute(); }
        function toggleLeg(idx, checked) { if (checked) visibleLegs.add(idx); else visibleLegs.delete(idx); const totalLegs = itineraryData[currentDay].length - 1; const allChecked = visibleLegs.size === totalLegs && totalLegs > 0; document.getElementById('toggle-all-routes').checked = allChecked; updateRouteVisibility(); renderAISuggestions(); }
        function toggleAllRoutes(checked) { document.querySelectorAll('#route-toggle-list input[type="checkbox"]').forEach(cb => cb.checked = checked); if (checked) for(let i=0; i < itineraryData[currentDay].length - 1; i++) visibleLegs.add(i); else visibleLegs.clear(); updateRouteVisibility(); renderAISuggestions(); }
        function updateRouteToggleUI() { const currentItinerary = itineraryData[currentDay]; const list = document.getElementById('route-toggle-list'); list.innerHTML = ''; for(let i=0; i < currentItinerary.length - 1; i++) { list.innerHTML += `<label class="flex items-center gap-1.5 cursor-pointer bg-white p-2.5 rounded-xl border border-slate-100 shadow-sm hover:bg-slate-50 transition"><input type="checkbox" checked onchange="toggleLeg(${i}, this.checked)" class="cursor-pointer accent-blue-600"><span class="font-bold truncate" style="color:${colorPalette[i % colorPalette.length]}">段落 ${i+1}➔${i+2}</span></label>`; } }
        function updateRouteVisibility() { routeLines.forEach(l => l.setMap(visibleLegs.has(l.legIndex) ? map : null)); routeLabels.forEach(l => l.setMap(visibleLegs.has(l.legIndex) ? map : null)); }
        window.onload = initMap;
    </script>
</body>
</html>