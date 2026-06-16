<?php

namespace Database\Seeders;

use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PresentationSeeder extends Seeder
{
    public function run(): void
    {
        // 山岳旅人
        $lm = User::firstOrCreate(
            ['email' => 'lm@gmail.com'],
            ['name' => '山岳旅人', 'password' => Hash::make('demo1234')]
        );

        Trip::where('user_id', $lm->id)->delete();

        Trip::create([
            'user_id' => $lm->id,
            'title' => '溪頭 & 杉林溪三日遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '溪頭自然教育園區', 'address' => '南投縣鹿谷鄉森林巷9號', 'location' => ['lat' => 23.6765, 'lng' => 120.8041], 'stay_time' => '180 分鐘', 'cost_estimate' => '$300', 'travel_time' => '步行 15 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '溪頭天空之橋', 'address' => '南投縣鹿谷鄉溪頭園區內', 'location' => ['lat' => 23.6780, 'lng' => 120.8060], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '溪頭大學池', 'address' => '南投縣鹿谷鄉溪頭園區內', 'location' => ['lat' => 23.6790, 'lng' => 120.8055], 'stay_time' => '45 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 8 分鐘', 'travel_mode' => 'WALKING'],
                ],
                '2' => [
                    ['name' => '杉林溪森林生態渡假園區', 'address' => '南投縣竹山鎮大鞍里杉林溪路8號', 'location' => ['lat' => 23.7178, 'lng' => 120.8389], 'stay_time' => '240 分鐘', 'cost_estimate' => '$500', 'travel_time' => '開車 35 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '杉林溪松瀧岩瀑布', 'address' => '南投縣竹山鎮杉林溪園區內', 'location' => ['lat' => 23.7200, 'lng' => 120.8410], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 20 分鐘', 'travel_mode' => 'WALKING'],
                ],
                '3' => [
                    ['name' => '竹山天梯', 'address' => '南投縣竹山鎮大鞍里大鞍路', 'location' => ['lat' => 23.6860, 'lng' => 120.6890], 'stay_time' => '90 分鐘', 'cost_estimate' => '$100', 'travel_time' => '開車 40 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '竹山鎮老街', 'address' => '南投縣竹山鎮下橫街', 'location' => ['lat' => 23.7534, 'lng' => 120.6842], 'stay_time' => '60 分鐘', 'cost_estimate' => '$150', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $lm->id,
            'title' => '合歡山賞雪之旅',
            'itinerary_data' => [
                '1' => [
                    ['name' => '合歡山主峰', 'address' => '南投縣仁愛鄉合歡山', 'location' => ['lat' => 24.1411, 'lng' => 121.2756], 'stay_time' => '120 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 60 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '武嶺', 'address' => '南投縣仁愛鄉台14甲線', 'location' => ['lat' => 24.1328, 'lng' => 121.2816], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '合歡山東峰', 'address' => '南投縣仁愛鄉合歡山東峰', 'location' => ['lat' => 24.1450, 'lng' => 121.2900], 'stay_time' => '120 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 45 分鐘', 'travel_mode' => 'WALKING'],
                ],
                '2' => [
                    ['name' => '清境農場青青草原', 'address' => '南投縣仁愛鄉定遠新村170號', 'location' => ['lat' => 24.0833, 'lng' => 121.1667], 'stay_time' => '180 分鐘', 'cost_estimate' => '$500', 'travel_time' => '開車 50 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '清境老英格蘭莊園', 'address' => '南投縣仁愛鄉榮光村仁和路170號', 'location' => ['lat' => 24.0841, 'lng' => 121.1680], 'stay_time' => '90 分鐘', 'cost_estimate' => '$200', 'travel_time' => '步行 5 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '清境小瑞士花園', 'address' => '南投縣仁愛鄉大同村壽亭巷', 'location' => ['lat' => 24.0820, 'lng' => 121.1650], 'stay_time' => '60 分鐘', 'cost_estimate' => '$150', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $lm->id,
            'title' => '阿里山日出行',
            'itinerary_data' => [
                '1' => [
                    ['name' => '阿里山森林遊樂區', 'address' => '嘉義縣阿里山鄉中正村7號', 'location' => ['lat' => 23.5142, 'lng' => 120.8031], 'stay_time' => '180 分鐘', 'cost_estimate' => '$300', 'travel_time' => '步行 20 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '阿里山神木群', 'address' => '嘉義縣阿里山鄉阿里山森林遊樂區內', 'location' => ['lat' => 23.5160, 'lng' => 120.8010], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 15 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '阿里山姊妹潭', 'address' => '嘉義縣阿里山鄉阿里山森林遊樂區內', 'location' => ['lat' => 23.5111, 'lng' => 120.7981], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 20 分鐘', 'travel_mode' => 'WALKING'],
                ],
                '2' => [
                    ['name' => '祝山觀日台', 'address' => '嘉義縣阿里山鄉祝山', 'location' => ['lat' => 23.5081, 'lng' => 120.8126], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '阿里山小火車 20 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '奮起湖老街', 'address' => '嘉義縣竹崎鄉中和村', 'location' => ['lat' => 23.4862, 'lng' => 120.7217], 'stay_time' => '60 分鐘', 'cost_estimate' => '$150', 'travel_time' => '開車 30 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        // 海岸旅人
        $ls = User::firstOrCreate(
            ['email' => 'ls@gmail.com'],
            ['name' => '海岸旅人', 'password' => Hash::make('demo1234')]
        );

        Trip::where('user_id', $ls->id)->delete();

        Trip::create([
            'user_id' => $ls->id,
            'title' => '墾丁海岸三日遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '墾丁大街', 'address' => '屏東縣恆春鎮墾丁路', 'location' => ['lat' => 21.9449, 'lng' => 120.7955], 'stay_time' => '90 分鐘', 'cost_estimate' => '$300', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '墾丁南灣海灘', 'address' => '屏東縣恆春鎮南灣路', 'location' => ['lat' => 21.9350, 'lng' => 120.7850], 'stay_time' => '150 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '貓鼻頭公園', 'address' => '屏東縣恆春鎮貓鼻頭', 'location' => ['lat' => 21.9136, 'lng' => 120.7413], 'stay_time' => '60 分鐘', 'cost_estimate' => '$50', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '2' => [
                    ['name' => '龍磐公園', 'address' => '屏東縣恆春鎮省道台26線', 'location' => ['lat' => 21.9059, 'lng' => 120.8296], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '鵝鑾鼻燈塔', 'address' => '屏東縣恆春鎮鵝鑾里燈塔路90號', 'location' => ['lat' => 21.9011, 'lng' => 120.8547], 'stay_time' => '60 分鐘', 'cost_estimate' => '$60', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '後壁湖漁港', 'address' => '屏東縣恆春鎮後壁湖', 'location' => ['lat' => 21.9544, 'lng' => 120.7762], 'stay_time' => '90 分鐘', 'cost_estimate' => '$400', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '3' => [
                    ['name' => '船帆石', 'address' => '屏東縣恆春鎮船帆路', 'location' => ['lat' => 21.9399, 'lng' => 120.8131], 'stay_time' => '45 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '小灣海灘', 'address' => '屏東縣恆春鎮墾丁路', 'location' => ['lat' => 21.9460, 'lng' => 120.7970], 'stay_time' => '120 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 8 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $ls->id,
            'title' => '北海岸絕景三日遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '野柳地質公園', 'address' => '新北市萬里區野柳里港東路167-1號', 'location' => ['lat' => 25.2047, 'lng' => 121.6900], 'stay_time' => '120 分鐘', 'cost_estimate' => '$80', 'travel_time' => '開車 50 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '老梅綠石槽', 'address' => '新北市石門區老梅里老梅街', 'location' => ['lat' => 25.2900, 'lng' => 121.5530], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 25 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '富貴角燈塔', 'address' => '新北市石門區富基里', 'location' => ['lat' => 25.3010, 'lng' => 121.5380], 'stay_time' => '45 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '2' => [
                    ['name' => '鼻頭角步道', 'address' => '新北市瑞芳區鼻頭路', 'location' => ['lat' => 25.1280, 'lng' => 121.9220], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 60 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '龍洞灣海洋公園', 'address' => '新北市貢寮區龍洞街', 'location' => ['lat' => 25.1150, 'lng' => 121.9280], 'stay_time' => '120 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '福隆海水浴場', 'address' => '新北市貢寮區福隆里興隆街', 'location' => ['lat' => 25.0240, 'lng' => 121.9440], 'stay_time' => '150 分鐘', 'cost_estimate' => '$100', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '3' => [
                    ['name' => '外木山海灘', 'address' => '基隆市中山區外木山', 'location' => ['lat' => 25.1620, 'lng' => 121.7070], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 50 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '潮境公園', 'address' => '基隆市中正區北寧路369巷', 'location' => ['lat' => 25.1530, 'lng' => 121.7840], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '八斗子漁港', 'address' => '基隆市中正區八斗街', 'location' => ['lat' => 25.1470, 'lng' => 121.7900], 'stay_time' => '60 分鐘', 'cost_estimate' => '$200', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $ls->id,
            'title' => '東海岸花東縱谷',
            'itinerary_data' => [
                '1' => [
                    ['name' => '花蓮七星潭', 'address' => '花蓮縣新城鄉七星街', 'location' => ['lat' => 24.0269, 'lng' => 121.6323], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '石梯坪', 'address' => '花蓮縣豐濱鄉石梯坪路', 'location' => ['lat' => 23.7300, 'lng' => 121.5230], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 60 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '磯崎海灘', 'address' => '花蓮縣豐濱鄉磯崎村', 'location' => ['lat' => 23.7900, 'lng' => 121.5500], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '2' => [
                    ['name' => '台東三仙台', 'address' => '台東縣成功鎮三仙里三仙台路74號', 'location' => ['lat' => 23.0959, 'lng' => 121.4167], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 90 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '台東小野柳', 'address' => '台東縣台東市松江路一段500號', 'location' => ['lat' => 22.7583, 'lng' => 121.1500], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 70 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '富岡漁港', 'address' => '台東縣台東市富岡里富岡路', 'location' => ['lat' => 22.7400, 'lng' => 121.1400], 'stay_time' => '45 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '3' => [
                    ['name' => '宜蘭南方澳漁港', 'address' => '宜蘭縣蘇澳鎮南方澳路', 'location' => ['lat' => 24.5975, 'lng' => 121.8440], 'stay_time' => '60 分鐘', 'cost_estimate' => '$300', 'travel_time' => '開車 120 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '宜蘭外澳沙灘', 'address' => '宜蘭縣頭城鎮濱海路五段', 'location' => ['lat' => 24.8381, 'lng' => 121.8339], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 40 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '烏石港衝浪沙灘', 'address' => '宜蘭縣頭城鎮濱海路二段', 'location' => ['lat' => 24.8520, 'lng' => 121.8460], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        // ── 版本 B：美食探索型 vs 文化藝術型 ─────────────────────

        $food = User::firstOrCreate(
            ['email' => 'food@demo.com'],
            ['name' => '美食探索者', 'password' => Hash::make('demo1234')]
        );
        Trip::where('user_id', $food->id)->delete();

        Trip::create([
            'user_id' => $food->id,
            'title' => '台南小吃深度三日遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '花園夜市', 'address' => '台南市北區海安路三段533號', 'location' => ['lat' => 23.0220, 'lng' => 120.2280], 'stay_time' => '120 分鐘', 'cost_estimate' => '$350', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '度小月擔仔麵', 'address' => '台南市中西區中正路16號', 'location' => ['lat' => 22.9990, 'lng' => 120.2006], 'stay_time' => '60 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '安平老街', 'address' => '台南市安平區安平路', 'location' => ['lat' => 22.9980, 'lng' => 120.1600], 'stay_time' => '90 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '2' => [
                    ['name' => '武廟周邊小吃街', 'address' => '台南市中西區永福路二段229巷', 'location' => ['lat' => 23.0010, 'lng' => 120.2030], 'stay_time' => '90 分鐘', 'cost_estimate' => '$250', 'travel_time' => '步行 15 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '大菜市（西門市場）', 'address' => '台南市中西區西門路二段', 'location' => ['lat' => 23.0026, 'lng' => 120.2065], 'stay_time' => '60 分鐘', 'cost_estimate' => '$150', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '保安路小吃一條街', 'address' => '台南市中西區保安路', 'location' => ['lat' => 22.9960, 'lng' => 120.2040], 'stay_time' => '60 分鐘', 'cost_estimate' => '$200', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                ],
                '3' => [
                    ['name' => '安平豆花', 'address' => '台南市安平區效忠街', 'location' => ['lat' => 22.9966, 'lng' => 120.1635], 'stay_time' => '45 分鐘', 'cost_estimate' => '$50', 'travel_time' => '開車 10 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '旗魚黑輪（海安路）', 'address' => '台南市中西區海安路一段', 'location' => ['lat' => 23.0063, 'lng' => 120.1987], 'stay_time' => '30 分鐘', 'cost_estimate' => '$80', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $food->id,
            'title' => '基隆海鮮夜市攻略',
            'itinerary_data' => [
                '1' => [
                    ['name' => '基隆廟口夜市', 'address' => '基隆市仁愛區仁三路', 'location' => ['lat' => 25.1270, 'lng' => 121.7420], 'stay_time' => '120 分鐘', 'cost_estimate' => '$400', 'travel_time' => '步行 5 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '崁仔頂魚市', 'address' => '基隆市仁愛區孝一路', 'location' => ['lat' => 25.1290, 'lng' => 121.7400], 'stay_time' => '60 分鐘', 'cost_estimate' => '$300', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '正濱漁港', 'address' => '基隆市中正區正濱路', 'location' => ['lat' => 25.1400, 'lng' => 121.7770], 'stay_time' => '60 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $food->id,
            'title' => '台北雙夜市挑戰',
            'itinerary_data' => [
                '1' => [
                    ['name' => '士林夜市', 'address' => '台北市士林區基河路101號', 'location' => ['lat' => 25.0879, 'lng' => 121.5240], 'stay_time' => '120 分鐘', 'cost_estimate' => '$350', 'travel_time' => '捷運 30 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '寧夏夜市', 'address' => '台北市大同區寧夏路', 'location' => ['lat' => 25.0555, 'lng' => 121.5169], 'stay_time' => '90 分鐘', 'cost_estimate' => '$250', 'travel_time' => '捷運 20 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '饒河夜市', 'address' => '台北市松山區八德路四段', 'location' => ['lat' => 25.0506, 'lng' => 121.5776], 'stay_time' => '90 分鐘', 'cost_estimate' => '$250', 'travel_time' => '捷運 20 分鐘', 'travel_mode' => 'TRANSIT'],
                ],
            ],
            'chat_history' => [],
        ]);

        $culture = User::firstOrCreate(
            ['email' => 'culture@demo.com'],
            ['name' => '文化藝術家', 'password' => Hash::make('demo1234')]
        );
        Trip::where('user_id', $culture->id)->delete();

        Trip::create([
            'user_id' => $culture->id,
            'title' => '台北藝文深度路線',
            'itinerary_data' => [
                '1' => [
                    ['name' => '國立故宮博物院', 'address' => '台北市士林區至善路二段221號', 'location' => ['lat' => 25.1022, 'lng' => 121.5485], 'stay_time' => '180 分鐘', 'cost_estimate' => '$350', 'travel_time' => '開車 30 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '台北當代藝術館', 'address' => '台北市大同區長安西路39號', 'location' => ['lat' => 25.0490, 'lng' => 121.5161], 'stay_time' => '90 分鐘', 'cost_estimate' => '$50', 'travel_time' => '捷運 25 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '松山文創園區', 'address' => '台北市信義區光復南路133號', 'location' => ['lat' => 25.0440, 'lng' => 121.5590], 'stay_time' => '120 分鐘', 'cost_estimate' => '$0', 'travel_time' => '捷運 15 分鐘', 'travel_mode' => 'TRANSIT'],
                ],
                '2' => [
                    ['name' => '國立台灣博物館', 'address' => '台北市中正區襄陽路2號', 'location' => ['lat' => 25.0440, 'lng' => 121.5120], 'stay_time' => '90 分鐘', 'cost_estimate' => '$30', 'travel_time' => '捷運 20 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '迪化街', 'address' => '台北市大同區迪化街一段', 'location' => ['lat' => 25.0580, 'lng' => 121.5097], 'stay_time' => '90 分鐘', 'cost_estimate' => '$200', 'travel_time' => '步行 20 分鐘', 'travel_mode' => 'WALKING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $culture->id,
            'title' => '台南古蹟文化深度遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '赤崁樓', 'address' => '台南市中西區民族路二段212號', 'location' => ['lat' => 23.0000, 'lng' => 120.2012], 'stay_time' => '90 分鐘', 'cost_estimate' => '$100', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '台灣文學館', 'address' => '台南市中西區中正路1號', 'location' => ['lat' => 22.9960, 'lng' => 120.2030], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '安平古堡', 'address' => '台南市安平區國勝路82號', 'location' => ['lat' => 23.0008, 'lng' => 120.1618], 'stay_time' => '90 分鐘', 'cost_estimate' => '$80', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '2' => [
                    ['name' => '奇美博物館', 'address' => '台南市仁德區文華路二段66號', 'location' => ['lat' => 22.9758, 'lng' => 120.2547], 'stay_time' => '180 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '億載金城', 'address' => '台南市安平區光州路3號', 'location' => ['lat' => 22.9890, 'lng' => 120.1530], 'stay_time' => '60 分鐘', 'cost_estimate' => '$50', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $culture->id,
            'title' => '台中文青藝術路線',
            'itinerary_data' => [
                '1' => [
                    ['name' => '國立台灣美術館', 'address' => '台中市西區五權西路一段2號', 'location' => ['lat' => 24.1418, 'lng' => 120.6765], 'stay_time' => '120 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '審計新村', 'address' => '台中市西區民生路368-1號', 'location' => ['lat' => 24.1464, 'lng' => 120.6728], 'stay_time' => '90 分鐘', 'cost_estimate' => '$150', 'travel_time' => '步行 15 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '台中文化創意產業園區', 'address' => '台中市南區復興路三段362號', 'location' => ['lat' => 24.1337, 'lng' => 120.6865], 'stay_time' => '90 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        // ── 版本 C：空帳號（提示詞完整度展示用）────────────────

        $newuser = User::firstOrCreate(
            ['email' => 'newuser@demo.com'],
            ['name' => '新手旅人', 'password' => Hash::make('demo1234')]
        );
        Trip::where('user_id', $newuser->id)->delete();

        // ── 版本 D：自然療癒型 vs 都市輕旅型 ──────────────────

        $nature = User::firstOrCreate(
            ['email' => 'nature@demo.com'],
            ['name' => '自然療癒者', 'password' => Hash::make('demo1234')]
        );
        Trip::where('user_id', $nature->id)->delete();

        Trip::create([
            'user_id' => $nature->id,
            'title' => '烏來瀑布溫泉秘境',
            'itinerary_data' => [
                '1' => [
                    ['name' => '烏來瀑布', 'address' => '新北市烏來區烏來里瀑布路', 'location' => ['lat' => 24.8697, 'lng' => 121.5539], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '步行 20 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => '內洞森林遊樂區', 'address' => '新北市烏來區孝義里信賢', 'location' => ['lat' => 24.8554, 'lng' => 121.5472], 'stay_time' => '120 分鐘', 'cost_estimate' => '$65', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '烏來老街溫泉', 'address' => '新北市烏來區烏來街', 'location' => ['lat' => 24.8670, 'lng' => 121.5561], 'stay_time' => '90 分鐘', 'cost_estimate' => '$300', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $nature->id,
            'title' => '宜蘭溫泉湖泊療癒之旅',
            'itinerary_data' => [
                '1' => [
                    ['name' => '礁溪溫泉', 'address' => '宜蘭縣礁溪鄉德陽路', 'location' => ['lat' => 24.8195, 'lng' => 121.7710], 'stay_time' => '120 分鐘', 'cost_estimate' => '$400', 'travel_time' => '開車 60 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '梅花湖', 'address' => '宜蘭縣冬山鄉大埤路150號', 'location' => ['lat' => 24.7128, 'lng' => 121.7943], 'stay_time' => '90 分鐘', 'cost_estimate' => '$150', 'travel_time' => '開車 30 分鐘', 'travel_mode' => 'DRIVING'],
                ],
                '2' => [
                    ['name' => '明池森林遊樂區', 'address' => '宜蘭縣大同鄉明池', 'location' => ['lat' => 24.6333, 'lng' => 121.5500], 'stay_time' => '180 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 60 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '棲蘭森林遊樂區', 'address' => '宜蘭縣大同鄉棲蘭路', 'location' => ['lat' => 24.6550, 'lng' => 121.5330], 'stay_time' => '120 分鐘', 'cost_estimate' => '$200', 'travel_time' => '開車 30 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $nature->id,
            'title' => '苗栗農場草莓慢活',
            'itinerary_data' => [
                '1' => [
                    ['name' => '飛牛牧場', 'address' => '苗栗縣通霄鎮南和里中山路168號', 'location' => ['lat' => 24.3497, 'lng' => 120.7064], 'stay_time' => '180 分鐘', 'cost_estimate' => '$300', 'travel_time' => '開車 60 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '大湖草莓觀光果園', 'address' => '苗栗縣大湖鄉大湖路', 'location' => ['lat' => 24.4216, 'lng' => 120.8544], 'stay_time' => '90 分鐘', 'cost_estimate' => '$250', 'travel_time' => '開車 40 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '明德水庫', 'address' => '苗栗縣頭屋鄉明德村', 'location' => ['lat' => 24.5041, 'lng' => 120.8214], 'stay_time' => '60 分鐘', 'cost_estimate' => '$0', 'travel_time' => '開車 30 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        $city = User::firstOrCreate(
            ['email' => 'city@demo.com'],
            ['name' => '都市輕旅人', 'password' => Hash::make('demo1234')]
        );
        Trip::where('user_id', $city->id)->delete();

        Trip::create([
            'user_id' => $city->id,
            'title' => '台北城市娛樂周末',
            'itinerary_data' => [
                '1' => [
                    ['name' => '西門町', 'address' => '台北市萬華區西門路', 'location' => ['lat' => 25.0422, 'lng' => 121.5080], 'stay_time' => '120 分鐘', 'cost_estimate' => '$500', 'travel_time' => '捷運 20 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '台北101觀景台', 'address' => '台北市信義區信義路五段7號', 'location' => ['lat' => 25.0338, 'lng' => 121.5645], 'stay_time' => '90 分鐘', 'cost_estimate' => '$600', 'travel_time' => '捷運 20 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => 'ATT 4 FUN', 'address' => '台北市信義區松壽路12號', 'location' => ['lat' => 25.0378, 'lng' => 121.5680], 'stay_time' => '90 分鐘', 'cost_estimate' => '$400', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                ],
                '2' => [
                    ['name' => '微風廣場', 'address' => '台北市松山區復興南路一段39號', 'location' => ['lat' => 25.0478, 'lng' => 121.5440], 'stay_time' => '120 分鐘', 'cost_estimate' => '$600', 'travel_time' => '捷運 20 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '南西SOGO', 'address' => '台北市中山區南京西路8號', 'location' => ['lat' => 25.0524, 'lng' => 121.5254], 'stay_time' => '90 分鐘', 'cost_estimate' => '$500', 'travel_time' => '捷運 15 分鐘', 'travel_mode' => 'TRANSIT'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $city->id,
            'title' => '高雄都市探索兩日遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '夢時代購物中心', 'address' => '高雄市前鎮區中華五路789號', 'location' => ['lat' => 22.5950, 'lng' => 120.3007], 'stay_time' => '150 分鐘', 'cost_estimate' => '$600', 'travel_time' => '捷運 30 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '三多商圈', 'address' => '高雄市苓雅區三多三路', 'location' => ['lat' => 22.6178, 'lng' => 120.3028], 'stay_time' => '90 分鐘', 'cost_estimate' => '$400', 'travel_time' => '捷運 15 分鐘', 'travel_mode' => 'TRANSIT'],
                    ['name' => '六合夜市', 'address' => '高雄市新興區六合二路', 'location' => ['lat' => 22.6264, 'lng' => 120.3026], 'stay_time' => '90 分鐘', 'cost_estimate' => '$300', 'travel_time' => '捷運 10 分鐘', 'travel_mode' => 'TRANSIT'],
                ],
            ],
            'chat_history' => [],
        ]);

        Trip::create([
            'user_id' => $city->id,
            'title' => '台中購物潮遊',
            'itinerary_data' => [
                '1' => [
                    ['name' => '勤美草悟道', 'address' => '台中市西區公益路與英才路口', 'location' => ['lat' => 24.1490, 'lng' => 120.6695], 'stay_time' => '90 分鐘', 'cost_estimate' => '$300', 'travel_time' => '開車 20 分鐘', 'travel_mode' => 'DRIVING'],
                    ['name' => '廣三SOGO', 'address' => '台中市西區台灣大道二段321號', 'location' => ['lat' => 24.1482, 'lng' => 120.6682], 'stay_time' => '120 分鐘', 'cost_estimate' => '$500', 'travel_time' => '步行 10 分鐘', 'travel_mode' => 'WALKING'],
                    ['name' => 'Tiger City老虎城', 'address' => '台中市西屯區河南路三段258號', 'location' => ['lat' => 24.1580, 'lng' => 120.6640], 'stay_time' => '90 分鐘', 'cost_estimate' => '$400', 'travel_time' => '開車 15 分鐘', 'travel_mode' => 'DRIVING'],
                ],
            ],
            'chat_history' => [],
        ]);

        $this->command->info('PresentationSeeder 完成！lm@gmail.com（山岳）& ls@gmail.com（海岸），密碼皆為 demo1234。');
    }
}
