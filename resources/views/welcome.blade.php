<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AI 智慧旅遊規劃系統 - 多天數存檔版</title>
    
    <script src="https://cdn.tailwindcss.com?plugins=typography"></script>
    <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCvAAdWjnxCHy6kfojvWq4iO4wKHOl14eY&libraries=places,marker&v=beta&language=zh-TW"></script>

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
        
        .day-tab { white-space: nowrap; transition: all 0.2s; }
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
    </style>
</head>
<body class="bg-slate-50 font-sans">

    <div class="flex h-screen w-full overflow-hidden">
        <div class="w-80 md:w-[420px] bg-white shadow-2xl z-20 flex flex-col flex-shrink-0 border-r border-slate-200">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2"><span>🚀</span> AI 旅程大師</h1>
                    <p class="text-blue-100 text-[10px] mt-1">Multi-Day Smart Planning</p>
                </div>
                <button onclick="saveFullTrip()" class="bg-indigo-500 hover:bg-indigo-400 text-white px-3 py-1.5 rounded-lg text-xs font-bold shadow-md transition flex items-center gap-1 border border-indigo-400">
                    <span>💾</span> 存檔
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scrollbar">
                
                <div class="flex items-center gap-2 overflow-x-auto pb-2 custom-scrollbar" id="day-tabs-container">
                    </div>

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
                        <span class="text-base">🤖</span> AI 智慧行程助手
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
                    <div id="itinerary-list" class="space-y-3 min-h-[100px]"></div>
                    <button onclick="calculateRoute()" id="route-btn" class="hidden w-full bg-emerald-600 text-white py-4 rounded-2xl font-extrabold text-base hover:bg-emerald-700 shadow-lg transition transform hover:scale-[1.02]">
                        計算 Day <span id="btn-day-num">1</span> 路徑
                    </button>
                </div>

                <div id="ai-suggestion-box" class="hidden p-5 bg-indigo-50 rounded-2xl border border-indigo-100 shadow-sm animate-in fade-in">
                    <h3 class="text-indigo-800 font-bold text-[13px] mb-3 flex items-center gap-1">🤖 AI 即時導引與轉乘建議</h3>
                    <div id="ai-suggestion-text" class="text-[12px] text-indigo-900 leading-relaxed space-y-4"></div>
                </div>

                <div id="detail-box" class="hidden p-5 bg-white rounded-2xl border border-slate-200 shadow-xl animate-in fade-in slide-in-from-bottom-4 duration-300">
                    <div id="detail-content" class="leading-relaxed"></div>
                </div>
            </div>
        </div>

        <div class="flex-1 relative"><div id="map"></div><button onclick="toggleTraffic()" class="absolute top-4 right-14 bg-white px-3 py-2 rounded-lg shadow-md z-10 text-[13px] font-bold hover:bg-slate-50 transition border border-slate-200">🚦 即時路況</button></div>
    </div>

    <script>
        let map, service, geocoder, directionsService, trafficLayer;
        let itineraryData = { 1: [] };
        let currentDay = 1;
        let dayCount = 1;

        let markers = [], routeLines = [], routeLabels = [];
        let currentMode = 'DRIVING', visibleLegs = new Set(), selectedRoutesMap = {}; 
        let lastShownDetailId = null;
        const colorPalette = ["#7c3aed", "#ec4899", "#f59e0b", "#10b981", "#3b82f6", "#ef4444", "#06b6d4"];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), { center: { lat: 24.162, lng: 120.640 }, zoom: 14, mapId: "4504f8b37365c3d0" });
            service = new google.maps.places.PlacesService(map); geocoder = new google.maps.Geocoder(); directionsService = new google.maps.DirectionsService();
            trafficLayer = new google.maps.TrafficLayer();
            
            const input = document.getElementById("pac-input");
            input.addEventListener("keydown", (e) => { if (e.key === "Enter") { e.preventDefault(); searchPlace(); } });
            const autocomplete = new google.maps.places.Autocomplete(input, { fields: ["name", "geometry", "place_id", "photos", "reviews", "types", "rating", "user_ratings_total", "formatted_address"] });
            autocomplete.addListener("place_changed", () => { const place = autocomplete.getPlace(); if (place.geometry) { processNewPlace(place); input.value = ""; } });

            renderDayTabs(); updateUI(); 
        }

        async function saveFullTrip() {
            let hasPoints = false;
            for (let day in itineraryData) {
                if (itineraryData[day].length > 0) hasPoints = true;
            }
            if (!hasPoints) {
                alert("目前還沒有加入任何景點，請先規劃行程再存檔喔！");
                return;
            }

            const title = prompt("請為這趟旅程取個名字：", "我的超讚旅行");
            if (!title) return;

            const payload = {
                title: title,
                itinerary_data: itineraryData 
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
                    alert("✅ 存檔成功！行程已安全存入資料庫！ (ID: " + result.id + ")");
                } else {
                    console.error("Server Error:", result);
                    alert("❌ 儲存失敗，請檢查後端錯誤：\n" + (result.message || JSON.stringify(result)));
                }
            } catch (error) {
                console.error("Network Error:", error);
                alert("🚨 發生連線錯誤，請確認 Laravel 伺服器 (php artisan serve) 有在運行。");
            }
        }

        function renderDayTabs() {
            const container = document.getElementById('day-tabs-container'); container.innerHTML = '';
            for (let i = 1; i <= dayCount; i++) {
                const btn = document.createElement('button');
                btn.className = `day-tab px-4 py-2 rounded-xl text-sm font-bold border border-slate-200 ${currentDay === i ? 'active' : 'bg-white text-slate-600'}`;
                btn.innerText = `Day ${i}`; btn.onclick = () => switchDay(i); container.appendChild(btn);
            }
            const addBtn = document.createElement('button'); addBtn.className = "px-3 py-2 rounded-xl bg-slate-100 text-slate-500 hover:bg-slate-200 font-bold border border-slate-200 transition";
            addBtn.innerHTML = "+"; addBtn.onclick = addNewDay; container.appendChild(addBtn);
        }

        function switchDay(day) { currentDay = day; clearAllRoutes(); renderDayTabs(); updateUI(); refreshMarkersOnly(); document.getElementById('ai-suggestion-box').classList.add('hidden'); document.getElementById('current-day-label').innerText = `📍 Day ${currentDay} 行程`; document.getElementById('btn-day-num').innerText = currentDay; }
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
            itineraryData[currentDay].push({ id: Date.now(), name: place.name || place.formatted_address, location: place.geometry.location, photo: place.photos ? place.photos[0].getUrl({ maxWidth: 400 }) : null, reviews: place.reviews || [], types: place.types || [], rating: place.rating || 0, user_ratings_total: place.user_ratings_total || 0, note: "" });
            updateUI(); map.panTo(place.geometry.location); clearAllRoutes(); refreshMarkersOnly();
        }

        function clearAllRoutes() { routeLines.forEach(l => l.setMap(null)); routeLabels.forEach(l => l.setMap(null)); routeLines = []; routeLabels = []; document.getElementById('ai-suggestion-box').classList.add('hidden'); document.getElementById('route-toggles').classList.add('hidden'); visibleLegs.clear(); selectedRoutesMap = {}; }

        function refreshMarkersOnly() {
            markers.forEach(m => m.setMap(null)); markers = []; const counts = {}; const currentItinerary = itineraryData[currentDay];
            currentItinerary.forEach((p, index) => {
                const key = `${p.location.lat().toFixed(6)},${p.location.lng().toFixed(6)}`; let lat = p.location.lat(), lng = p.location.lng();
                if (counts[key]) { lat += (counts[key] * 0.00022); lng += (counts[key] * 0.00022); counts[key]++; } else { counts[key] = 1; }
                const glyph = document.createElement('div'); glyph.className = 'bg-blue-600 text-white w-8 h-8 rounded-full flex items-center justify-center font-bold text-base shadow-lg border-2 border-white'; glyph.innerText = (index + 1).toString();
                markers.push(new google.maps.marker.AdvancedMarkerElement({ map, position: {lat, lng}, content: glyph }));
            });
        }

        async function calculateRoute() {
            const currentItinerary = itineraryData[currentDay]; if (currentItinerary.length < 2) return;
            clearAllRoutes(); document.getElementById('route-toggles').classList.remove('hidden');
            const aiBox = document.getElementById('ai-suggestion-box'), aiText = document.getElementById('ai-suggestion-text');
            aiBox.classList.remove('hidden'); aiText.innerHTML = `正在計算 Day ${currentDay} 最佳路徑...`;
            for(let i=0; i < currentItinerary.length - 1; i++) { visibleLegs.add(i); selectedRoutesMap[i] = { index: 0, result: null }; }
            updateRouteToggleUI(); 
            for (let i = 0; i < currentItinerary.length - 1; i++) {
                const results = await requestRouteByMode(currentItinerary[i].location, currentItinerary[i+1].location, currentMode);
                if (results && results.routes) { selectedRoutesMap[i].result = results; drawLeg(results.routes, i); } else { aiText.innerHTML = `<span class="text-red-500 font-bold">❌ 第 ${i+1} 段計算失敗。</span>`; return; }
            }
            renderAISuggestions(); updateRouteVisibility(); checkOptimalRouteSuggestion();
        }

        async function requestRouteByMode(origin, dest, mode) {
            let apiMode = google.maps.TravelMode[mode] || mode; let request = { origin: origin, destination: dest, travelMode: apiMode, provideRouteAlternatives: true };
            if (mode === 'TWO_WHEELER') { request.travelMode = google.maps.TravelMode.DRIVING; request.avoidHighways = true; }
            if (mode === 'DRIVING' || mode === 'TWO_WHEELER') { request.drivingOptions = { departureTime: new Date(), trafficModel: google.maps.TrafficModel.BEST_GUESS }; }
            return new Promise((resolve) => { directionsService.route(request, (res, status) => resolve(status === 'OK' ? res : null)); });
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
            const currentItinerary = itineraryData[currentDay]; if (currentItinerary.length < 3 || currentMode === 'TRANSIT') return;
            let optMode = (currentMode === 'TWO_WHEELER') ? 'TWO_WHEELER' : currentMode;
            let request = { origin: currentItinerary[0].location, destination: currentItinerary[currentItinerary.length - 1].location, waypoints: currentItinerary.slice(1, -1).map(p => ({ location: p.location, stopover: true })), optimizeWaypoints: true, travelMode: google.maps.TravelMode[optMode] || optMode };
            if (currentMode === 'TWO_WHEELER') { request.travelMode = google.maps.TravelMode.DRIVING; request.avoidHighways = true; }
            directionsService.route(request, (res, status) => {
                if (status === 'OK') {
                    const opt = res.routes[0].waypoint_order; let swap = "";
                    for(let i=0; i<opt.length; i++) if(opt[i] !== i) { swap = `將 <span class="text-red-600 font-bold">第 ${i+2} 站</span> 與 <span class="text-red-600 font-bold">第 ${opt[i]+2} 站</span> 互換更省時！`; break; }
                    if (swap) { document.getElementById('ai-suggestion-text').innerHTML += `<div class="mt-2 text-indigo-700 font-bold">💡 AI 建議：${swap}</div>`; }
                }
            });
        }

        function renderAISuggestions() {
            const currentItinerary = itineraryData[currentDay]; const box = document.getElementById('ai-suggestion-text'); let html = "";
            if (currentMode === 'TRANSIT') {
                for (let i = 0; i < currentItinerary.length - 1; i++) {
                    if (!visibleLegs.has(i) || !selectedRoutesMap[i].result) continue; const leg = selectedRoutesMap[i].result.routes[selectedRoutesMap[i].index].legs[0];
                    html += `<div class="mb-4 border-b border-indigo-100 pb-3">📍 第 ${i + 1} 段詳情：${currentItinerary[i].name} ➔ ${currentItinerary[i+1].name}`;
                    leg.steps.forEach(step => {
                        const dur = step.duration.text.replace("mins", "分鐘");
                        if (step.travel_mode === 'TRANSIT') { html += `<div class="transit-step"><span class="text-blue-600 font-extrabold text-[13px]">🚌 搭乘 ${step.transit.line.short_name || step.transit.line.name}</span><br><span class="text-[11px] text-slate-500">於 ${step.transit.departure_stop.name} 上車 (約 ${dur})</span></div>`; } else { html += `<div class="transit-step"><span class="text-slate-600 text-[12px]">🚶 ${step.instructions.replace(/Walk to /i, "步行至 ")}</span></div>`; }
                    }); html += `</div>`;
                } box.innerHTML = html || "✅ 已規劃最佳方案。";
            } else { box.innerHTML = "<div class='text-[12px]'>✅ 路線已優化。可點擊地圖淺色線條對比時間。</div>"; }
        }

        function toggleDetail(index) { const box = document.getElementById('detail-box'); const currentItinerary = itineraryData[currentDay]; if (lastShownDetailId === currentItinerary[index].id && !box.classList.contains('hidden')) { box.classList.add('hidden'); } else { showPlaceDetail(currentItinerary[index]); lastShownDetailId = currentItinerary[index].id; } }

        async function showPlaceDetail(point) {
            const box = document.getElementById('detail-box'); const content = document.getElementById('detail-content'); box.classList.remove('hidden');
            const hour = new Date().getHours(); const types = point.types; let cat = "景點", advice = "環境穩定舒適。";
            if (types.some(t => ['school', 'university'].includes(t))) { cat = "🏫 教育設施"; advice = "⚠️ 內部不開放參訪。非教職員禁止進入。"; } else if (types.some(t => ['restaurant', 'cafe', 'food'].includes(t))) { cat = "🍴 餐飲場所"; if ((hour >= 11 && hour <= 13) || (hour >= 17 && hour <= 19)) advice = "🍴 <b>正值用餐尖峰</b>，建議提早訂位。"; } else if (types.includes('premise') || types.includes('street_address')) { cat = "🏠 私人區域"; advice = "🤫 <b>私人住宅區</b>。請保持安靜並尊重居民隱私。"; }
            let revHtml = ""; if (point.reviews && point.reviews.length > 0) {
                let p = [], c = []; point.reviews.forEach(r => { if (r.rating >= 4 && p.length < 2) p.push(r.text.substring(0, 50)); if (r.rating <= 3 && c.length < 2) c.push(r.text.substring(0, 50)); });
                revHtml = `<div class="mt-4 grid grid-cols-2 gap-3"><div class="bg-green-50 p-3 rounded-xl border border-green-100 shadow-sm"><p class="text-green-700 font-extrabold text-[13px] mb-1.5 flex items-center gap-1">✅ 讚點心得</p><ul class="text-[12px] text-green-800 space-y-1.5">${p.map(x=>`<li>• ${x}...</li>`).join('') || '<li>整體環境整潔</li>'}</ul></div><div class="bg-red-50 p-3 rounded-xl border border-red-100 shadow-sm"><p class="text-red-700 font-extrabold text-[13px] mb-1.5 flex items-center gap-1">⚠️ 旅客注意</p><ul class="text-[12px] text-red-800 space-y-1.5">${c.map(x=>`<li>• ${x}...</li>`).join('') || '<li>無特殊差評</li>'}</ul></div></div>`;
            } content.innerHTML = `${point.photo ? `<img src="${point.photo}" class="rounded-2xl mb-4 w-full h-48 object-cover shadow-md">` : ''}<div class="text-slate-800 font-bold text-[17px] flex justify-between items-center px-1"><span>📍 ${point.name}</span><span class="bg-blue-50 text-blue-600 px-3 py-1 rounded-lg text-[11px] border border-blue-100 font-extrabold uppercase tracking-tighter">${cat}</span></div><div class="mt-3 p-4 bg-slate-50 rounded-xl text-slate-600 text-[14px] flex gap-3 border border-slate-100 shadow-sm"><div class="flex-shrink-0 text-lg">🕒</div><div class="flex-1 leading-relaxed"><span class="font-bold text-slate-700">AI 真實提醒：</span><span>${advice}</span></div></div>${revHtml}`;
        }

        function editNote(id) { const currentItinerary = itineraryData[currentDay]; const item = currentItinerary.find(p => p.id === id); if (item) { const newNote = prompt(`為「${item.name}」加上個人備註（例如：買伴手禮、必吃滷肉飯）：`, item.note || ""); if (newNote !== null) { item.note = newNote; updateUI(); } } }

        function updateUI() {
            const currentItinerary = itineraryData[currentDay]; const list = document.getElementById('itinerary-list'); document.getElementById('point-count').innerText = `${currentItinerary.length} 個地點`; document.getElementById('route-btn').classList.toggle('hidden', currentItinerary.length < 2);
            if (currentItinerary.length === 0) { list.innerHTML = `<div class="text-center text-slate-400 py-8 text-sm">📍 Day ${currentDay} 尚未新增地點</div>`; return; }
            list.innerHTML = currentItinerary.map((p, i) => `
                <div class="bg-white border-l-4 border-blue-500 rounded-xl p-4 shadow-sm flex justify-between items-center group animate-in slide-in-from-left duration-200">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[11px] text-blue-500 font-bold uppercase tracking-wider">站點 ${i+1}</p>
                        <div class="flex items-center gap-2">
                            <p class="font-bold text-slate-800 text-[15px] truncate">${p.name}</p>
                            <button onclick="toggleDetail(${i})" class="text-blue-500 hover:text-blue-700 transition flex-shrink-0" title="查看資訊">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </button>
                        </div>
                        ${p.note ? `<p class="text-[12px] text-emerald-600 mt-1 flex items-center gap-1 font-bold">📝 ${p.note}</p>` : ''}
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="editNote(${p.id})" class="text-slate-300 hover:text-emerald-500 transition px-1" title="加入備註"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg></button>
                        <button onclick="moveItem(${i}, -1)" class="move-btn p-1 text-slate-300 hover:text-blue-600 ${i === 0 ? 'invisible' : ''}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        <button onclick="moveItem(${i}, 1)" class="move-btn p-1 text-slate-300 hover:text-blue-600 ${i === currentItinerary.length-1 ? 'invisible' : ''}"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        <button onclick="removeItem(${p.id})" class="text-slate-200 hover:text-red-500 transition px-1 ml-1">✕</button>
                    </div>
                </div>`).join('');
        }

        // 🤖 植入的 AI 對話發送與處理邏輯
        async function askAIForItinerary() {
            const promptValue = document.getElementById('ai-chat-prompt').value;
            if (!promptValue) return alert("請輸入您的想法喔！");

            const btn = document.getElementById('ai-gen-btn');
            const btnText = document.getElementById('ai-btn-text');
            
            btn.disabled = true;
            btn.classList.add('opacity-70');
            btnText.innerText = "AI 正在規劃中...";

            try {
                const response = await fetch('/ai-generate', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ prompt: promptValue, mode: currentMode })
                });

                const data = await response.json();
                
                if (response.ok && data.status === 'success') {
                    // 💡 將 AI 建議回填到當前天數的行程，同時顯示「車程」與「停留時間」
                    itineraryData[currentDay] = data.suggestions.map((item, index) => {
                        // 判斷是否為第一站 (出發點)
                        let isFirstStop = index === 0;
                        
                        // 讀取 AI 產生的各項數據
                        let travel = isFirstStop ? '📍 出發點' : (item.travel_time ? `🚗 車程 ${item.travel_time}` : '');
                        let stay = item.stay_time ? `⏱️ 停留 ${item.stay_time}` : '';
                        let cost = item.cost_estimate ? `💰 ${item.cost_estimate}` : '';
                        let reason = item.reason ? `💡 ${item.reason}` : '';
                        
                        // 組合備註字串 (用 filter 巧妙地把空字串濾掉，並用 ｜ 隔開)
                        let richNote = [travel, stay, cost, reason].filter(Boolean).join(' ｜ ');

                        return {
                            id: Date.now() + index,
                            name: item.name,
                            location: new google.maps.LatLng(item.lat, item.lng),
                            note: richNote,
                            rating: 5,
                            types: ['tourist_attraction'],
                            reviews: []
                        };
                    });

                    updateUI(); // 更新清單介面
                    refreshMarkersOnly(); // 更新地圖圖標
                    
                    document.getElementById('ai-suggestion-box').classList.remove('hidden');
                    document.getElementById('ai-suggestion-text').innerHTML = `<p class="italic text-indigo-700">「${data.ai_message}」</p>`;
                    
                    alert("✨ AI 已為您產生專屬行程！");
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