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

        $this->command->info('PresentationSeeder 完成！lm@gmail.com（山岳）& ls@gmail.com（海岸），密碼皆為 demo1234。');
    }
}
