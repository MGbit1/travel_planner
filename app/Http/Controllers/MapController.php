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

    public function generateAI(Request $request) {
        // 每位登入用戶每 60 秒限制 3 次，未登入則以 IP 為鍵
        $rateLimitKey = 'ai-generate-' . (auth()->id() ?? $request->ip());
        $executed = RateLimiter::attempt($rateLimitKey, 3, function () { return true; }, 60);
        if (!$executed) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            return response()->json([
                'status'        => 'rate_limit',
                'message'       => "AI 請求次數已達上限，請等待 {$seconds} 秒後再試。",
                'retry_after'   => $seconds,
            ], 429);
        }

        $userPrompt = $request->input('prompt');
        $history = $request->input('history', []);
        $allItineraries = $request->input('all_itineraries', []);
        $apiKey = trim(env('GEMINI_API_KEY'));

        $currentDay = $request->input('current_day', 1);

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

        // 💡 這裡就是我們把「緊箍咒」加進去的地方！（第 11 點）
        $systemRules = "你是一位擁有「超強地理方向感」且「高情商貼心」的頂級旅遊規劃大師。\n" .
                       $itineraryContext . "\n" .
                       "【⚠️ 核心強制規則（絕對遵守）】：\n" .
                       "1. 【極致順路】：排出的景點順序必須是地理動線最短，絕不走回頭路。\n" .
                       "2. 【交通與停車補貼】：若交通模式為 DRIVING 或 TWO_WHEELER，【必須】在各站 travel_time 自動額外增加 10-15 分鐘的停車緩衝時間，並在理由中簡述停車便利性（如：有專屬停車場或附近有收費站）。\n" .
                       "3. 【出發地優先】：若提及「出發地」或「住家」，【必須】設為第 1 站。\n" .
                       "4. 【回程限制】：除非使用者明確要求，否則【絕對不要】擅自安排回家行程。\n" .
                       "5. 【精準導航意圖】：若需求只是「A地到B地」，請【只】輸出這 2 個地點。\n" .
                       "6. 【交通模式與多模式時間】：全域 	ravel_mode 回傳整趟行程建議的主要交通方式。每個景點【必須】填寫 	ransport_times，包含至少 WALKING 和 DRIVING 兩種時間估算（若適合騎車請也加上 BICYCLING），時間請依實際距離估算，格式如「20 分鐘」。\n" .
                       "7. 【溫馨預警與備案】：優先遵守使用者時間，若有妥協（如錯過夕陽、店家未開）須在 ai_message 提醒，並附上一個『雨天備案建議』。\n" .
                       "8. 【跨天與指定天數操作】：若使用者「明確指定」要操作其他天數（例如說：「第二天要跟第一天一樣」、「幫我新增第三天」），請務必在 JSON 中輸出對應的天數（例如 `\"day\": 2`），系統才能正確分配。\n" .
                       "9. 【預算與免費標註】：cost_estimate 須具體（如：門票 $200、餐費 $400），免費則寫 $0。\n" .
                       "10. 【預設當前視角】：使用者目前正停留在「Day {$currentDay}」。只要使用者【沒有明確提到其他天數】，他所有的指令（包含「給我第一天行程」、「新增景點」）都是想在當前的「Day {$currentDay}」進行操作。請將結果放在「Day {$currentDay}」回傳，切勿擅自存到別天。\n" .
                       "11. 【絕對實體地點與停車模式】：如果使用者要求尋找住宿或餐廳，請給出具體店名。景點名稱保持乾淨。此外，請【必須】分析該景點的停車情境，並在 suggestions JSON 中增加一個欄位 `\"parking_mode\": \"...\"`：\n" .
                        "    - 若為飯店、大型百貨、有自備大型停車場的景點，填寫 `\"INTERNAL\"`。理由需提到自備停車場。\n" .
                        "    - 若為夜市、老街、戶外公園、台中港等需在外找位置的景點，填寫 `\"EXTERNAL\"`。理由需提醒附近車位狀況。\n" .
                        "12. 【景區內交通主動確認】：若行程中多個景點位於同一封閉式園區（如溪頭、阿里山、合歡山、墾丁各景區、觀光農場、主題樂園等），【必須】做到以下兩點：\n" .
                        "    (1) 先以「最保守交通方式（步行）」為假設輸出一份完整暫定行程，讓使用者有行程可參考。\n" .
                        "    (2) 同時在 `ai_message` 說明假設並主動詢問（例如：「我先以步行方式估算了園區內時間，若您打算租借電動代步車、單車或有接駁車，請告訴我，我會重新調整！」）。\n" .
                        "    【禁止】只問問題而不附上暫定行程。\n    【必須】：封閉園區內各景點的 	ravel_mode 欄位設為 WALKING（或其他實際可用模式），	ravel_time 按步行距離估算，不可沿用全程的 DRIVING。\n" .
                        "13. 【行程總時數把關】：每天所有景點的 transport_times.WALKING 與 stay_time 加總後，若超過 10 小時，【必須】在 `ai_message` 明確警告使用者「行程偏緊」，並具體建議可刪減哪個景點，以及刪減後預估省下多少時間。\n" .
                        "14. 【用餐時段錨點】：若單日行程超過 4 小時，【必須】在午餐時段（11:30–13:00）與晚餐時段（17:30–19:30）各安排至少一個用餐景點（餐廳或小吃），不可讓使用者連續 5 小時以上沒有用餐安排。若使用者已自行指定餐廳，則以使用者為準。\n\n" .
                        "請嚴格以純 JSON 格式回覆：\n" .
                        "{\n" .
                        "  \"ai_message\": \"...\",\n" .
                        "  \"travel_mode\": \"...\",\n" .
                        "  \"days\": [\n" .
                        "    {\n" .
                        "      \"day\": 1,\n" .
                        "      \"suggestions\": [\n" .
                        "        {\n" .
                        "          \"name\": \"...\", \"lat\": ..., \"lng\": ..., \n" .
                        "          \"travel_mode\": \"建議的主要交通方式代碼\",\n" .
                        "          \"transport_times\": { \"WALKING\": \"...\", \"DRIVING\": \"...\", \"BICYCLING\": \"...\" },\n" .
                        "          \"stay_time\": \"...\", \n" .
                        "          \"cost_estimate\": \"...\", \"reason\": \"...\",\n" .
                        "          \"parking_mode\": \"INTERNAL 或 EXTERNAL\"\n" .
                        "        }\n" .
                        "      ]\n" .
                        "    }\n" .
                        "  ]\n" .
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

        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key={$apiKey}";
        $maxRetries = 3;
        $response = null;

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->withoutVerifying()
                    ->timeout(120)
                    ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                    ->post($url, ['contents' => $contents]);

                if ($response->status() === 429) {
                    \Log::warning("Gemini API 429 配額超限", [
                        'user_id' => auth()->id(),
                        'body'    => $response->body(),
                    ]);
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'AI 服務今日使用量已達上限，請明天（台灣時間早上 8 點後）再試。',
                    ], 429);
                }

                if ($response->status() === 503) {
                    \Log::warning("Gemini API 503（第 {$attempt} 次）", [
                        'user_id' => auth()->id(),
                        'attempt' => $attempt,
                        'body'    => $response->body(),
                    ]);
                    if ($attempt < $maxRetries) {
                        sleep(2);
                        continue;
                    }
                    return response()->json([
                        'status'  => 'error',
                        'message' => 'AI 服務目前流量較高，請稍後再試。',
                    ], 503);
                }

                break;

            } catch (\Exception $e) {
                if ($attempt === $maxRetries) {
                    return response()->json([
                        'status'  => 'error',
                        'message' => '【本機系統崩潰】：' . $e->getMessage(),
                    ], 500);
                }
                sleep(2);
            }
        }

        try {
            if (!$response->successful()) {
                \Log::error('Gemini API 非成功回應', [
                    'status'  => $response->status(),
                    'body'    => $response->body(),
                    'user_id' => auth()->id(),
                ]);
                return response()->json([
                    'status'  => 'error',
                    'message' => 'AI 服務暫時無法使用，請稍後再試。',
                ], $response->status());
            }

            $resData = $response->json();

            if (!isset($resData['candidates'][0]['content']['parts'][0]['text'])) {
                \Log::error('Gemini API 回應結構異常', ['response' => $resData]);
                return response()->json([
                    'status'  => 'error',
                    'message' => '【AI 回應格式異常】：請稍後再試。',
                ], 500);
            }

            $rawText   = $resData['candidates'][0]['content']['parts'][0]['text'];
            $cleanJson = preg_replace('/^```json\s*|```\s*$/m', '', trim($rawText));
            $result    = json_decode($cleanJson, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('Gemini JSON 解析失敗', ['raw' => $rawText, 'error' => json_last_error_msg()]);
                return response()->json([
                    'status'  => 'error',
                    'message' => '【AI 回傳格式錯誤】：無法解析行程資料，請再試一次。',
                ], 500);
            }

            return response()->json([
                'status'      => 'success',
                'ai_message'  => $result['ai_message']  ?? '規劃完成！',
                'travel_mode' => $result['travel_mode'] ?? 'DRIVING',
                'days'        => $this->enrichWithDistanceMatrix($result['days'] ?? []),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => '【本機系統崩潰】：' . $e->getMessage(),
            ], 500);
        }
    }

    private function enrichWithDistanceMatrix(array $days): array
    {
        $googleKey = env('GOOGLE_MAPS_API_KEY');
        if (empty($googleKey)) return $days;

        $modes    = ['walking', 'driving', 'bicycling'];
        $meta     = [];
        $poolJobs = [];

        foreach ($days as $di => $day) {
            $sugs = $day['suggestions'] ?? [];
            for ($i = 1; $i < count($sugs); $i++) {
                $origin = $sugs[$i - 1]['lat'] . ',' . $sugs[$i - 1]['lng'];
                $dest   = $sugs[$i]['lat']     . ',' . $sugs[$i]['lng'];
                foreach ($modes as $mode) {
                    $key = "{$di}_{$i}_{$mode}";
                    $meta[$key]     = ['di' => $di, 'si' => $i, 'mode' => strtoupper($mode)];
                    $poolJobs[$key] = ['origin' => $origin, 'dest' => $dest, 'mode' => $mode];
                }
            }
        }

        if (empty($poolJobs)) return $days;

        try {
            $responses = Http::pool(function ($pool) use ($poolJobs, $googleKey) {
                $reqs = [];
                foreach ($poolJobs as $key => $job) {
                    $reqs[] = $pool->as($key)
                        ->withoutVerifying()
                        ->withOptions(['curl' => [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4]])
                        ->timeout(15)
                        ->get('https://maps.googleapis.com/maps/api/distancematrix/json', [
                            'origins'      => $job['origin'],
                            'destinations' => $job['dest'],
                            'mode'         => $job['mode'],
                            'language'     => 'zh-TW',
                            'key'          => $googleKey,
                        ]);
                }
                return $reqs;
            });

            foreach ($responses as $key => $res) {
                if (!isset($meta[$key])) continue;
                try {
                    if (!$res->successful()) continue;
                    $data    = $res->json();
                    $element = $data['rows'][0]['elements'][0] ?? [];
                    if (($data['status'] ?? '') !== 'OK' || ($element['status'] ?? '') !== 'OK') continue;
                    $duration = $element['duration']['text'] ?? null;
                    if (!$duration) continue;
                    $m = $meta[$key];
                    $days[$m['di']]['suggestions'][$m['si']]['transport_times'][$m['mode']] = $duration;
                } catch (\Exception $e) {
                    continue;
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Distance Matrix API 失敗，保留 AI 估算', ['error' => $e->getMessage()]);
        }

        return $days;
    }
}