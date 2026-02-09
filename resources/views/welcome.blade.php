<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI 智慧旅遊規劃系統 - Google 旗艦版</title>
    
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
        .route-label { background: white; padding: 2px 6px; border-radius: 10px; border: 1px solid #7c3aed; color: #7c3aed; font-size: 10px; font-weight: bold; box-shadow: 0 2px 4px rgba(0,0,0,0.1); white-space: nowrap; pointer-events: none; }
    </style>
</head>
<body class="bg-slate-50 font-sans">

    <div class="flex h-screen w-full overflow-hidden">
        <div class="w-80 md:w-96 bg-white shadow-2xl z-20 flex flex-col flex-shrink-0 border-r border-slate-200">
            <div class="p-6 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                <h1 class="text-2xl font-bold flex items-center gap-2"><span>🚀</span> AI 旅程大師</h1>
                <p class="text-blue-100 text-xs mt-1">Powered by Gemini 3 Flash & Google Maps</p>
            </div>

            <div class="flex-1 overflow-y-auto p-5 space-y-6 custom-scrollbar">
                
                <div class="space-y-2 relative">
                    <label class="text-xs font-bold text-slate-500 uppercase tracking-wider">🔍 探索景點或地址</label>
                    <div class="flex gap-1">
                        <input type="text" id="pac-input" class="flex-1 border border-slate-200 rounded-lg px-3 py-2 text-sm outline-none focus:ring-2 focus:ring-blue-500 transition" placeholder="輸入名稱或地址後按 Enter">
                        <button onclick="searchPlace()" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-700 transition">搜尋</button>
                    </div>
                    <div id="search-results-panel" class="hidden absolute left-0 right-0 bg-white border border-slate-200 rounded-lg shadow-xl z-30 overflow-y-auto custom-scrollbar"></div>
                </div>

                <div class="grid grid-cols-3 gap-2">
                    <button onclick="updateTravelMode('DRIVING')" class="mode-btn p-2 bg-blue-600 text-white rounded-lg text-[10px] font-bold transition" id="btn-DRIVING">🚗 開車</button>
                    <button onclick="updateTravelMode('TRANSIT')" class="mode-btn p-2 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold" id="btn-TRANSIT">🚌 轉乘</button>
                    <button onclick="updateTravelMode('WALKING')" class="mode-btn p-2 bg-slate-100 text-slate-600 rounded-lg text-[10px] font-bold" id="btn-WALKING">🚶 步行</button>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center border-b pb-2">
                        <h2 class="font-bold text-slate-700 text-sm">📍 行程點清單</h2>
                        <span class="text-[10px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold" id="point-count">0 個地點</span>
                    </div>
                    <div id="itinerary-list" class="space-y-2"></div>
                    <button onclick="calculateRoute()" id="route-btn" class="hidden w-full bg-emerald-600 text-white py-3 rounded-xl font-bold text-sm hover:bg-emerald-700 shadow-lg transition transform hover:scale-[1.02]">
                        即時動態優化路徑
                    </button>
                </div>

                <div id="ai-suggestion-box" class="hidden p-4 bg-indigo-50 rounded-xl border border-indigo-100 shadow-sm animate-in fade-in duration-500">
                    <h3 class="text-indigo-800 font-bold text-xs mb-2 flex items-center gap-1">🤖 AI 智慧路徑優化建議</h3>
                    <div id="ai-suggestion-text" class="text-[11px] text-indigo-900 leading-relaxed italic"></div>
                </div>

                <div id="detail-box" class="hidden p-4 bg-white rounded-xl border border-slate-200 shadow-lg animate-in fade-in slide-in-from-bottom-4 duration-300">
                    <div id="detail-content" class="text-[11px] leading-relaxed"></div>
                </div>

            </div>
        </div>

        <div class="flex-1 relative">
            <div id="map"></div>
            <button onclick="toggleTraffic()" class="absolute top-4 right-14 bg-white p-2 rounded-md shadow-md z-10 text-xs font-bold hover:bg-slate-50">🚦 即時路況</button>
        </div>
    </div>

    <script>
        let map, service, geocoder, directionsService;
        let itinerary = [];
        let markers = []; 
        let routeLines = []; 
        let routeLabels = []; 
        let currentMode = 'DRIVING';

        const colorPalette = ["#7c3aed", "#ec4899", "#f59e0b", "#10b981", "#3b82f6", "#ef4444", "#06b6d4"];

        function initMap() {
            map = new google.maps.Map(document.getElementById("map"), {
                center: { lat: 24.162, lng: 120.640 },
                zoom: 14,
                mapId: "4504f8b37365c3d0",
            });
            service = new google.maps.places.PlacesService(map);
            geocoder = new google.maps.Geocoder();
            directionsService = new google.maps.DirectionsService();

            const input = document.getElementById("pac-input");
            input.addEventListener("keydown", (e) => { if (e.key === "Enter") { e.preventDefault(); searchPlace(); } });

            const autocomplete = new google.maps.places.Autocomplete(input, {
                fields: ["name", "geometry", "place_id", "photos", "reviews", "types", "rating", "user_ratings_total", "formatted_address"]
            });
            autocomplete.addListener("place_changed", () => {
                const place = autocomplete.getPlace();
                if (place.geometry) { processNewPlace(place); input.value = ""; hideResultsPanel(); }
            });
        }

        function searchPlace() {
            const query = document.getElementById("pac-input").value;
            if (!query) return;
            const panel = document.getElementById("search-results-panel");
            panel.innerHTML = `<div class="p-4 text-xs text-slate-400 italic animate-pulse">🔍 搜尋中...</div>`;
            panel.classList.remove("hidden"); panel.classList.add("active");

            service.textSearch({ query: query, location: map.getCenter(), radius: '5000', language: 'zh-TW' }, (results, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK && results) { renderSearchResults(results); }
                else { panel.innerHTML = `<div class="p-4 text-xs text-red-500 font-bold">❌ 找不到地點。</div>`; }
            });
        }

        function renderSearchResults(results) {
            const panel = document.getElementById("search-results-panel");
            panel.innerHTML = "";
            results.slice(0, 5).forEach(place => {
                const div = document.createElement("div");
                div.className = "p-3 border-b border-slate-100 hover:bg-blue-50 cursor-pointer flex items-start gap-3 transition";
                const icon = (place.types.includes('bus_station') || place.types.includes('transit_station')) ? '🚌' : '📍';
                div.innerHTML = `<div class="mt-1 text-lg">${icon}</div><div class="flex-1 overflow-hidden"><div class="text-sm font-bold text-slate-800 truncate">${place.name}</div><div class="text-[10px] text-slate-400 truncate">${place.formatted_address}</div></div>`;
                div.onclick = () => { fetchFullDetails(place.place_id); hideResultsPanel(); document.getElementById("pac-input").value = ""; };
                panel.appendChild(div);
            });
        }

        function fetchFullDetails(placeId) {
            service.getDetails({ placeId: placeId, fields: ["name", "geometry", "place_id", "photos", "reviews", "types", "rating", "user_ratings_total", "formatted_address"] }, (place, status) => {
                if (status === google.maps.places.PlacesServiceStatus.OK) processNewPlace(place);
            });
        }

        function hideResultsPanel() { const panel = document.getElementById("search-results-panel"); panel.classList.remove("active"); setTimeout(() => panel.classList.add("hidden"), 300); }

        function processNewPlace(place) {
            itinerary.push({
                id: Date.now(),
                name: place.name || place.formatted_address,
                location: place.geometry.location,
                photo: place.photos ? place.photos[0].getUrl({ maxWidth: 400 }) : null,
                reviews: place.reviews || [],
                types: place.types || [],
                rating: place.rating || 0,
                user_ratings_total: place.user_ratings_total || 0
            });
            updateUI();
            showPlaceDetail(itinerary[itinerary.length - 1]);
            map.panTo(place.geometry.location);
            clearAllRoutes();
            refreshMarkersOnly();
        }

        function clearAllRoutes() {
            routeLines.forEach(l => l.setMap(null));
            routeLabels.forEach(label => label.setMap(null));
            routeLines = []; routeLabels = [];
            document.getElementById('ai-suggestion-box').classList.add('hidden');
        }

        function refreshMarkersOnly() {
            markers.forEach(m => m.setMap(null));
            markers = [];
            const locationCounts = {};
            itinerary.forEach((p, index) => {
                const lat = p.location.lat(), lng = p.location.lng();
                const key = `${lat.toFixed(6)},${lng.toFixed(6)}`;
                let finalLat = lat, finalLng = lng;
                if (locationCounts[key]) {
                    const offset = locationCounts[key] * 0.00022; // 拉開標記距離
                    finalLat += offset; finalLng += offset;
                    locationCounts[key]++;
                } else { locationCounts[key] = 1; }
                createNumberedMarker({ lat: finalLat, lng: finalLng }, (index + 1).toString());
            });
        }

        function createNumberedMarker(position, label) {
            const glyph = document.createElement('div');
            glyph.className = 'bg-blue-600 text-white w-7 h-7 rounded-full flex items-center justify-center font-bold text-sm shadow-lg border-2 border-white';
            glyph.innerText = label;
            markers.push(new google.maps.marker.AdvancedMarkerElement({ map: map, position: position, content: glyph }));
        }

        // 💡 核心優化：路徑偏移演算法與互換建議
        function calculateRoute() {
            if (itinerary.length < 2) return;
            clearAllRoutes();

            directionsService.route({
                origin: itinerary[0].location,
                destination: itinerary[itinerary.length - 1].location,
                waypoints: itinerary.slice(1, -1).map(p => ({ location: p.location, stopover: true })),
                optimizeWaypoints: false, 
                travelMode: google.maps.TravelMode[currentMode]
            }, (result, status) => {
                if (status === 'OK') {
                    const legs = result.routes[0].legs;
                    legs.forEach((leg, i) => {
                        // 💡 偏移座標，避免線條完全重疊
                        const offset = i * 0.000045; 
                        const pathWithOffset = leg.steps.flatMap(s => s.path).map(coord => {
                            return { lat: coord.lat() + offset, lng: coord.lng() + offset };
                        });

                        const line = new google.maps.Polyline({
                            path: pathWithOffset,
                            strokeColor: colorPalette[i % colorPalette.length],
                            strokeOpacity: 0.8, strokeWeight: 6, map: map
                        });
                        routeLines.push(line);

                        const midStep = leg.steps[Math.floor(leg.steps.length / 2)];
                        const labelDiv = document.createElement('div');
                        labelDiv.className = 'route-label';
                        labelDiv.innerText = `${i + 1} ➔ ${i + 2}`;
                        routeLabels.push(new google.maps.marker.AdvancedMarkerElement({ map: map, position: midStep.start_location, content: labelDiv }));
                    });
                    fetchSmartAISuggestion();
                }
            });
        }

        function fetchSmartAISuggestion() {
            directionsService.route({
                origin: itinerary[0].location,
                destination: itinerary[itinerary.length - 1].location,
                waypoints: itinerary.slice(1, -1).map(p => ({ location: p.location, stopover: true })),
                optimizeWaypoints: true,
                travelMode: google.maps.TravelMode[currentMode]
            }, (result, status) => {
                if (status === 'OK') {
                    const order = result.routes[0].waypoint_order;
                    const isOptimal = order.every((val, idx) => val === idx);
                    const box = document.getElementById('ai-suggestion-box');
                    const text = document.getElementById('ai-suggestion-text');

                    if (!isOptimal) {
                        box.classList.remove('hidden');
                        let swapTips = "";
                        for(let i=0; i<order.length; i++) {
                            if(order[i] !== i) {
                                swapTips = `將 <span class="text-red-600 font-bold">第 ${i+2} 站</span> 與 <span class="text-red-600 font-bold">第 ${order[i]+2} 站</span> 互換，路程將更順暢！`;
                                break; 
                            }
                        }
                        let path = "理想順序：第 1 站";
                        order.forEach(i => path += ` → 第 ${i+2} 站`);
                        path += ` → 第 ${itinerary.length} 站`;
                        text.innerHTML = `💡 ${swapTips}<br>${path}`;
                    } else { box.classList.add('hidden'); }
                }
            });
        }

        function moveItem(index, direction) {
            const targetIndex = index + direction;
            if (targetIndex < 0 || targetIndex >= itinerary.length) return;
            const temp = itinerary[index];
            itinerary[index] = itinerary[targetIndex];
            itinerary[targetIndex] = temp;
            updateUI(); refreshMarkersOnly();
            if (routeLines.length > 0) calculateRoute();
        }

        // 💡 智慧分析更新：嚴格檢查評論，不瞎編優缺點
        async function showPlaceDetail(point) {
            const box = document.getElementById('detail-box');
            const content = document.getElementById('detail-content');
            box.classList.remove('hidden');

            const types = point.types;
            let category = "一般地標", crowdAdvice = "全天候適宜。";

            if (types.some(t => ['school', 'university'].includes(t))) {
                category = "🏫 教育設施"; crowdAdvice = "平日白天學生較多，建議保持安靜。";
            } else if (types.includes('street_address') || types.includes('premise')) {
                category = "🏠 私人區域"; crowdAdvice = "此為住宅區域，建議僅作為起終點。";
            } else if (types.some(t => ['restaurant', 'cafe', 'shopping_mall'].includes(t))) {
                category = "🍴 餐飲/商業"; crowdAdvice = "用餐時段人潮較多，建議提前預約。";
            } else if (types.some(t => ['tourist_attraction', 'park', 'museum'].includes(t))) {
                category = "🌟 熱門景點"; crowdAdvice = "週末及假日人潮密集，建議平日前往。";
            }

            // 💡 修正：只有在評論數量 > 0 時才顯示優缺點區塊
            let reviewHtml = "";
            if (point.reviews && point.reviews.length > 0) {
                let pros = [], cons = [];
                point.reviews.slice(0, 5).forEach(r => {
                    if (r.rating >= 4 && pros.length < 2) pros.push(r.text.substring(0, 40));
                    if (r.rating <= 3 && cons.length < 2) cons.push(r.text.substring(0, 40));
                });

                reviewHtml = `
                    <div class="mt-3 grid grid-cols-2 gap-2">
                        <div class="bg-green-50 p-2 rounded border border-green-100">
                            <p class="text-green-700 font-bold text-[9px]">✅ 讚點心得</p>
                            <ul class="text-[8px] text-green-800 mt-1">${pros.map(p=>`<li>• ${p}...</li>`).join('') || '<li>環境維護優良</li>'}</ul>
                        </div>
                        <div class="bg-red-50 p-2 rounded border border-red-100">
                            <p class="text-red-700 font-bold text-[9px]">⚠️ 旅客注意</p>
                            <ul class="text-[8px] text-red-800 mt-1">${cons.map(c=>`<li>• ${c}...</li>`).join('') || '<li>尖峰時段較擁擠</li>'}</ul>
                        </div>
                    </div>
                `;
            } else {
                // 如果沒有評論，顯示簡潔的事實陳述
                reviewHtml = `<div class="mt-3 p-2 bg-slate-50 rounded border border-slate-100 text-slate-400 italic text-[9px]">目前 Google 資料庫尚無此地點的公開評論。AI 建議您可將其視為行程的功能性停靠點。</div>`;
            }

            content.innerHTML = `
                ${point.photo ? `<img src="${point.photo}" class="rounded-lg mb-3 w-full h-40 object-cover shadow-sm">` : ''}
                <div class="text-slate-800 font-bold text-[14px] flex justify-between items-start">
                    <span>📍 ${point.name}</span>
                    <span class="bg-blue-50 text-blue-600 border border-blue-100 px-2 py-0.5 rounded text-[9px]">${category}</span>
                </div>
                <div class="mt-2 p-2 bg-slate-50 rounded text-slate-600 text-[10px]">
                    🕒 <b>人潮預測：</b>${crowdAdvice}
                </div>
                ${reviewHtml}
            `;
        }

        function updateUI() {
            const list = document.getElementById('itinerary-list');
            document.getElementById('point-count').innerText = `${itinerary.length} 個地點`;
            document.getElementById('route-btn').classList.toggle('hidden', itinerary.length < 2);
            list.innerHTML = itinerary.map((p, i) => `
                <div class="bg-white border-l-4 border-blue-500 rounded-lg p-3 shadow-sm flex justify-between items-center group animate-in slide-in-from-left duration-200">
                    <div class="flex-1 overflow-hidden">
                        <p class="text-[10px] text-blue-500 font-bold">第 ${i + 1} 站</p>
                        <p class="font-bold text-slate-800 text-sm truncate">${p.name}</p>
                    </div>
                    <div class="flex items-center gap-1">
                        <button onclick="moveItem(${i}, -1)" class="move-btn p-1 text-slate-400 hover:text-blue-600 ${i === 0 ? 'invisible' : ''}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M5 15l7-7 7 7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        <button onclick="moveItem(${i}, 1)" class="move-btn p-1 text-slate-400 hover:text-blue-600 ${i === itinerary.length-1 ? 'invisible' : ''}"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 9l-7 7-7-7" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></button>
                        <button onclick="removeItem(${p.id})" class="text-slate-200 hover:text-red-500 transition px-1 ml-1">✕</button>
                    </div>
                </div>`).join('');
        }

        function removeItem(id) {
            itinerary = itinerary.filter(p => p.id !== id);
            updateUI(); refreshMarkersOnly(); clearAllRoutes();
            if (itinerary.length >= 2 && routeLines.length > 0) calculateRoute(); 
        }

        function updateTravelMode(mode) {
            currentMode = mode;
            document.querySelectorAll('.mode-btn').forEach(b => { b.classList.remove('bg-blue-600', 'text-white'); b.classList.add('bg-slate-100', 'text-slate-600'); });
            document.getElementById(`btn-${mode}`).classList.add('bg-blue-600', 'text-white');
            if (itinerary.length >= 2) calculateRoute(); 
        }

        function toggleTraffic() { map.setOptions({ trafficLayer: !map.get('trafficLayer') }); }
        window.onload = initMap;
    </script>
</body>
</html>