<?php

namespace App\Services;

use App\Models\Trip;
use Illuminate\Support\Facades\Log;

class PlaceStatisticsService
{
    // 過濾掉非景點的名稱關鍵字
    private const EXCLUDE_KEYWORDS = ['出發', '回家', '返程', '起點', '終點'];

    /**
     * 統計所有行程中景點出現次數，回傳排序後的景點陣列。
     * 使用 cursor() 逐筆讀取，避免 Trip::all() 一次載入全部資料造成 OOM。
     *
     * @param int $limit 回傳前幾名
     */
    public function getTopPlaces(int $limit = 15): array
    {
        $placeCounts = [];

        // 只撈需要的欄位，並用 cursor 串流，不把全表塞進記憶體
        Trip::whereNotNull('itinerary_data')
            ->select('id', 'itinerary_data')
            ->cursor()
            ->each(function (Trip $trip) use (&$placeCounts) {
                $itinerary = $this->decodeItinerary($trip);
                if (!is_array($itinerary)) return;

                foreach ($itinerary as $places) {
                    if (!is_array($places)) continue;
                    foreach ($places as $place) {
                        $name = trim($place['name'] ?? '');
                        if (empty($name) || $this->shouldExclude($name)) continue;

                        if (!isset($placeCounts[$name])) {
                            $placeCounts[$name] = [
                                'name'   => $name,
                                'count'  => 0,
                                'photo'  => $place['photo']  ?? null,
                                'rating' => $place['rating'] ?? 0,
                                'types'  => $place['types']  ?? [],
                            ];
                        }
                        $placeCounts[$name]['count']++;
                    }
                }
            });

        usort($placeCounts, fn($a, $b) => $b['count'] <=> $a['count']);

        return array_slice($placeCounts, 0, $limit);
    }

    private function decodeItinerary(Trip $trip): ?array
    {
        if (is_array($trip->itinerary_data)) {
            return $trip->itinerary_data;
        }

        if (is_string($trip->itinerary_data)) {
            $decoded = json_decode($trip->itinerary_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::warning("Trip #{$trip->id} 的 itinerary_data JSON 格式錯誤：" . json_last_error_msg());
                return null;
            }
            return $decoded;
        }

        return null;
    }

    private function shouldExclude(string $name): bool
    {
        foreach (self::EXCLUDE_KEYWORDS as $keyword) {
            if (str_contains($name, $keyword)) return true;
        }
        // 過濾純門牌地址（如「138號」、「3巷」），但保留帶有其他文字的名稱
        if (preg_match('/^\d+\s*(號|巷)$/', $name)) return true;

        return false;
    }
}
