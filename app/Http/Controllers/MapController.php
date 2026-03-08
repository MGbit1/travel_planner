<?php

namespace App\Http\Controllers;

use App\Models\Place;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\RateLimiter;

class MapController extends Controller
{
    public function index()
    {
        $places = Place::all();
        return view('welcome', compact('places'));
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'address' => 'nullable|string|max:255', 
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);
        
        Place::create($validated);
        return back()->with('success', '景點已成功新增！');
    }

    public function destroy($id){
        $place = Place::findOrFail($id);
        $place->delete();
        return back()->with('success', '景點已成功刪除！');
    }

    public function aiPlan()
    {
        $executed = RateLimiter::attempt(
            'ai-plan-unique-limit',
            1, 
            function() { return true; },
            60 
        );

        if (!$executed) {
            return response()->json([
                'message' => '⚠️ 伺服器冷卻中！請等待 60 秒後再按，避免 API Key 被鎖定。'
            ], 429);
        }

        $places = Place::all();
        if ($places->isEmpty()) return response()->json(['message' => '目前清單是空的！']);

        $apiKey = trim(env('GEMINI_API_KEY'));
        $locationContext = $places->map(fn($p) => "- {$p->title} (座標: {$p->latitude}, {$p->longitude})")->implode("\n");

        $prompt = "你是一位專業導遊。請根據以下座標規劃順路行程：\n{$locationContext}\n\n" .
                  "任務：\n" .
                  "1. 規劃最順路順序並用繁體中文簡介。\n" .
                  "2. 重要：請在回覆的最末端，務必用這一行格式寫出順序，且名稱必須與清單完全相同：\n" .
                  "[Order: 景點名稱1, 景點名稱2, ...]";

        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";

            // 💡 手術：加入 CURL_IPRESOLVE_V4 避免 DNS 解析卡死
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying() 
                ->timeout(120) // 延長超時時間
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 // 強制使用 IPv4
                    ]
                ])
                ->post($url, [
                    'contents' => [['parts' => [['text' => $prompt]]]]
                ]);

            if ($response->successful()) {
                $resData = $response->json();
                $aiText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? 'AI 回傳格式異常';
                return response()->json(['message' => $aiText]);
            }

            return response()->json([
                'message' => 'Google API 報錯 - ' . ($response->json()['error']['message'] ?? '未知錯誤'),
                'detail' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['message' => '連線異常：' . $e->getMessage()], 500);
        }
    }

    // 🤖 智慧對話式生成行程功能 (具備記憶與全局視野版)
    public function generateAI(Request $request) {
        $userPrompt = $request->input('prompt');
        $history = $request->input('history', []); 
        $allItineraries = $request->input('all_itineraries', []); 
        $apiKey = trim(env('GEMINI_API_KEY'));
    
        if (empty($apiKey)) {
            return response()->json(['status' => 'error', 'message' => '系統找不到 API 金鑰，請確認 .env 設定！'], 500);
        }

        // 整理跨天數小抄
        $itineraryContext = "【目前系統中其他天數的已規劃行程】：\n";
        $hasData = false;
        foreach ($allItineraries as $day => $points) {
            if (!empty($points)) {
                $names = array_column($points, 'name');
                $itineraryContext .= "Day {$day}: " . implode(' ➔ ', $names) . "\n";
                $hasData = true;
            }
        }
        if (!$hasData) $itineraryContext .= "尚無資料。\n";
       
        // 系統規則
        $systemRules = "你是一位專業旅遊規劃師。請根據使用者需求規劃順路行程。\n" .
                       $itineraryContext . "\n" .
                       "【⚠️ 重要規則】：\n" .
                       "1. 若提及「出發地」或「住家」，【必須】設為第 1 站。\n" .
                       "2. 【回程限制】：除非使用者明確說出「當天來回」、「來回」或「回家」等字眼，否則【絕對不要】擅自幫使用者安排回程。使用者說去哪，就排到哪裡為止。\n" .
                       "3. 【跨天數複製大法】：若使用者說「我要 Day X 的行程」或「跟 Day X 一樣」，請務必直接看上方的【已規劃行程】小抄，並「完全複製」該天所有的景點名稱，再配合他要求的新條件(如改變交通工具或出發地)。\n" .
                       "4. 若使用者說「照剛剛的行程」或有延續先前對話的語意，請讀取歷史紀錄中的路線並加上他要求的新條件(例如新起點)。\n" .
                       "5. 【精準判斷導航意圖】：若使用者的語意只是單純的「從A地到B地」（例如：「我要從...到...」、「...去...」），而沒有提到「安排行程」、「玩」、「順遊」等字眼，請【絕對不要】擅加任何休息站或景點，請【只】輸出他提到的那 2 個地點。只有在明確要求旅遊規劃時才擴充到 3-6 個景點。\n" .
                       "6. 【交通模式判斷】：請判斷使用者使用的交通工具，回傳對應的代碼（DRIVING=開車, TWO_WHEELER=機車/騎車, TRANSIT=大眾運輸, WALKING=步行, BICYCLING=單車）。若無提及則預設為 DRIVING。\n" .
                       "7. 【預算與時間】：預估「從上一站到此地點的車程時間」及「建議停留時間」，並精準分配「建議花費上限」。\n" .
                       "請嚴格以純 JSON 格式回覆，不要有任何 Markdown 標記，格式如下：\n" .
                       "{\n" .
                       "  \"ai_message\": \"對行程的整體描述與預算總結\",\n" .
                       "  \"travel_mode\": \"DRIVING\",\n" .
                       "  \"suggestions\": [\n" .
                       "    {\n" .
                       "      \"name\": \"地點名稱\", \n" .
                       "      \"lat\": 緯度, \n" .
                       "      \"lng\": 經度,\n" .
                       "      \"travel_time\": \"從上一站到這裡的車程 (例如: 約30分鐘)\",\n" .
                       "      \"stay_time\": \"建議停留多久 (例如: 1.5小時)\",\n" .
                       "      \"cost_estimate\": \"建議花費 (例如: 約300元)\",\n" .
                       "      \"reason\": \"推薦原因或消費建議\"\n" .
                       "    }\n" .
                       "  ]\n" .
                       "}";
    
        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                'role' => $msg['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $msg['text']]]
            ];
        }

        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => "使用者最新需求：「{$userPrompt}」\n\n【⚠️ 系統強制規則（請務必遵守）】：\n{$systemRules}"]]
        ];
    
        try {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";
    
            // 💡 手術核心：延長為 120 秒，且強制使用 IPv4，避開 DNS 黑洞
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->timeout(120) // 給予 AI 更充足的思考時間
                ->withOptions([
                    'curl' => [
                        CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4 // 終極殺招：強制走 IPv4
                    ]
                ])
                ->post($url, [
                    'contents' => $contents
                ]);
    
            if ($response->successful()) {
                $resData = $response->json();
                $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                $cleanJson = preg_replace('/^```json\s*|```\s*$/', '', trim($rawText));
                $result = json_decode($cleanJson, true);
    
                return response()->json([
                    'status' => 'success',
                    'ai_message' => $result['ai_message'] ?? '這是為您專屬規劃的行程',
                    'travel_mode' => $result['travel_mode'] ?? 'DRIVING',
                    'suggestions' => $result['suggestions'] ?? []
                ]);
            }
            
            $errorData = $response->json();
            $googleError = $errorData['error']['message'] ?? '未知錯誤';
            return response()->json(['status' => 'error', 'message' => 'Google API 報錯：' . $googleError], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => '伺服器連線異常：' . $e->getMessage()], 500);
        }
    }
}