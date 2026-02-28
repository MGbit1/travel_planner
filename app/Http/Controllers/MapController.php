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
        // ✨ 1. 伺服器端防噴鎖定：每 60 秒只允許執行 1 次
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

            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying() 
                ->timeout(60)
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

    // 🤖 新增：智慧對話式生成行程功能
    // 🤖 智慧對話式生成行程功能
    public function generateAI(Request $request) {
        $userPrompt = $request->input('prompt');
        $apiKey = trim(env('GEMINI_API_KEY'));
    
        if (empty($apiKey)) {
            return response()->json(['status' => 'error', 'message' => '系統找不到 API 金鑰，請確認 .env 設定！'], 500);
        }

       
        // 💡 雙管齊下版：同時要求 AI 預估「車程」與「停留時間」
        $systemPrompt = "你是一位專業旅遊規劃師。請根據使用者需求：「{$userPrompt}」，規劃 3 到 6 個地點的順路行程。\n" .
                        "【⚠️ 重要規則】：\n" .
                        "1. 若提及「出發地」或「住家」，【必須】設為第 1 站。\n" .
                        "2. 若提及「當天來回」，【必須】將出發地同時設為最後 1 站。\n" .
                        "3. 【預算分配】：請根據使用者的「總預算」進行拆解，精準分配每個景點的「建議花費上限」。\n" .
                        "4. 【時間規劃】：請同時預估「從上一站到此地點的車程時間」以及「建議在此停留的時間」（出發點與回家點可免填停留時間）。\n" .
                        "請嚴格以純 JSON 格式回覆，不要有任何 Markdown 標記，格式如下：\n" .
                        "{\n" .
                        "  \"ai_message\": \"對行程的整體描述與預算總結\",\n" .
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
    
        try {
            // 💡 使用與您原本 aiPlan 相同的 Gemini 模型
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";
    
            $response = Http::withHeaders(['Content-Type' => 'application/json'])
                ->withoutVerifying()
                ->timeout(60)
                ->post($url, [
                    'contents' => [['parts' => [['text' => $systemPrompt]]]]
                ]);
    
            if ($response->successful()) {
                $resData = $response->json();
                $rawText = $resData['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                $cleanJson = preg_replace('/^```json\s*|```\s*$/', '', trim($rawText));
                $result = json_decode($cleanJson, true);
    
                return response()->json([
                    'status' => 'success',
                    'ai_message' => $result['ai_message'] ?? '這是為您專屬規劃的行程',
                    'suggestions' => $result['suggestions'] ?? []
                ]);
            }
            
            // 🚨 關鍵修改：把 Google 真正的報錯訊息印出來給前端看
            $errorData = $response->json();
            $googleError = $errorData['error']['message'] ?? '未知錯誤';
            return response()->json(['status' => 'error', 'message' => 'Google API 報錯：' . $googleError], 500);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => '伺服器連線異常：' . $e->getMessage()], 500);
        }
    }
    }