<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Http\Request;

class RankingController extends Controller
{
    public function index()
    {
        // 抓取系統中所有的行程
        $trips = Trip::all();
        $placeCounts = [];

        // 拆解每一個行程，統計景點出現次數
        foreach ($trips as $trip) {
            $itinerary = is_string($trip->itinerary_data) ? json_decode($trip->itinerary_data, true) : $trip->itinerary_data;
            
            if (!$itinerary) continue;

            foreach ($itinerary as $day => $places) {
                foreach ($places as $place) {
                    $name = $place['name'] ?? '未知地點';
                    
                    // 💡 過濾掉「出發」、「回家」或是具體門牌號碼等非景點的字詞
                    if (str_contains($name, '出發') || str_contains($name, '回家') || str_contains($name, '返程') || preg_match('/[0-9]+號|[0-9]+巷/', $name)) {
                        continue;
                    }

                    // 如果這個景點還沒被統計過，就初始化它
                    if (!isset($placeCounts[$name])) {
                        $placeCounts[$name] = [
                            'name' => $name,
                            'count' => 0,
                            'photo' => $place['photo'] ?? null,
                            'rating' => $place['rating'] ?? 0,
                            'types' => $place['types'] ?? [],
                        ];
                    }
                    
                    // 次數 +1
                    $placeCounts[$name]['count']++;
                }
            }
        }

        // 依照出現次數由高到低排序 (次數相同則依賴原本順序)
        usort($placeCounts, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        // 只取前 15 名的熱門景點
        $topPlaces = array_slice($placeCounts, 0, 15);

        return view('ranking.index', compact('topPlaces'));
    }
}