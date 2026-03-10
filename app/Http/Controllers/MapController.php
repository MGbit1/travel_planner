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
            // 💡 修正 1：改用 v1 路徑與穩定的 1.5-flash 模型
            $url = "https://generativelanguage.googleapis.com/v1/models/gemini-1.5-flash:generateContent?key={$apiKey}";

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->timeout(120)
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
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

    public function generateAI(Request $request) {
        $userPrompt = $request->input('prompt');
        $history = $request->input('history', []);
        $allItineraries = $request->input('all_itineraries', []);
        $apiKey = trim(env('GEMINI_API_KEY'));

        if (empty($apiKey)) {
            return response()->json(['status' => 'error', 'message' => '系統找不到 API 金鑰，請確認 .env 設定！'], 500);
        }

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

        $systemRules = "你是一位擁有「超強地理方向感」且「高情商貼心」的頂級旅遊規劃大師。\n" .
                       $itineraryContext . "\n" .
                       "【⚠️ 核心強制規則（絕對遵守）】：\n" .
                       "1. 【極致順路】：排出的景點順序必須是地理動線最短，絕不走回頭路。\n" .
                       "2. 【交通與停車補貼】：若交通模式為 DRIVING 或 TWO_WHEELER，【必須】在各站 travel_time 自動額外增加 10-15 分鐘的停車緩衝時間，並在理由中簡述停車便利性（如：有專屬停車場或附近有收費站）。\n" .
                       "3. 【出發地優先】：若提及「出發地」或「住家」，【必須】設為第 1 站。\n" .
                       "4. 【回程限制】：除非使用者明確要求，否則【絕對不要】擅自安排回家行程。\n" .
                       "5. 【精準導航意圖】：若需求只是「A地到B地」，請【只】輸出這 2 個地點。\n" .
                       "6. 【交通模式】：正確回傳代碼（DRIVING, TWO_WHEELER, TRANSIT, WALKING, BICYCLING）。\n" .
                       "7. 【溫馨預警與備案】：優先遵守使用者時間，若有妥協（如錯過夕陽、店家未開）須在 ai_message 提醒，並附上一個『雨天備案建議』。\n" .
                       "8. 【多天數分群】：自動判斷需求天數，依照 Day 1, Day 2... 分類打包。\n" .
                       "9. 【預算與免費標註】：cost_estimate 須具體（如：門票 $200、餐費 $400），免費則寫 $0。\n\n" .
                       "請嚴格以純 JSON 格式回覆，不含 Markdown：\n" .
                       "{\n" .
                       "  \"ai_message\": \"行程總結、重要預警（含停車提醒）與雨天備案\",\n" .
                       "  \"travel_mode\": \"DRIVING\",\n" .
                       "  \"days\": [\n" .
                       "    {\n" .
                       "      \"day\": 1,\n" .
                       "      \"suggestions\": [\n" .
                       "        {\n" .
                       "          \"name\": \"地點名稱\", \"lat\": 緯度, \"lng\": 經度, \n" .
                       "          \"travel_time\": \"含停車緩衝的車程\", \"stay_time\": \"建議停留\", \n" .
                       "          \"cost_estimate\": \"預估花費\", \"reason\": \"推薦理由（含停車建議）\"\n" .
                       "        }\n" .
                       "      ]\n" .
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
            // 💡 解決 404：改用 v1beta 路徑，因為 Preview 模型只在這個路徑下
            // 💡 解決 429：改用 Flash 系列，額度比 Pro 高很多
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";
    
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->timeout(120) 
                ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                ->post($url, ['contents' => $contents]);
    
            if (!$response->successful()) {
                // 這行能幫您抓出 Google 真正的抱怨（是 404 還是 429）
                return response()->json([
                    'status' => 'error', 
                    'message' => '【Google 拒絕連線】：' . $response->body()
                ], $response->status());
            }
            
            $resData = $response->json();
            $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
            $cleanJson = preg_replace('/^```json\s*|```\s*$/', '', trim($rawText));
            $result = json_decode($cleanJson, true);

            return response()->json([
                'status' => 'success',
                'ai_message' => $result['ai_message'] ?? '規劃完成！',
                'travel_mode' => $result['travel_mode'] ?? 'DRIVING',
                'days' => $result['days'] ?? [] 
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => '【本機系統崩潰】：' . $e->getMessage()
            ], 500);
        }
        }
    }
