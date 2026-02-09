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
        // 這能確保你的 API Key 即使在報錯後也能有足夠時間「冷卻」
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
            // ✨ 2. 修正模型名稱：補回 -preview
            // 在 v1beta 版本中，這個型號必須寫完整：gemini-3-flash-preview
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

            // 若 Google 報錯，回傳具體原因
            return response()->json([
                'message' => 'Google API 報錯 - ' . ($response->json()['error']['message'] ?? '未知錯誤'),
                'detail' => $response->json()
            ], $response->status());

        } catch (\Exception $e) {
            return response()->json(['message' => '連線異常：' . $e->getMessage()], 500);
        }
    }
}