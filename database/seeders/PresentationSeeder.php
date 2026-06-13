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

        if (Trip::where('user_id', $lm->id)->doesntExist()) {
            Trip::create([
                'user_id' => $lm->id,
                'title' => '溪頭 & 杉林溪三日遊',
                'itinerary_data' => [
                    '1' => [
                        ['name' => '溪頭自然教育園區', 'stay_time' => '180 分鐘'],
                        ['name' => '溪頭天空之橋', 'stay_time' => '60 分鐘'],
                        ['name' => '溪頭大學池', 'stay_time' => '45 分鐘'],
                    ],
                    '2' => [
                        ['name' => '杉林溪森林生態渡假園區', 'stay_time' => '240 分鐘'],
                        ['name' => '杉林溪松瀧岩瀑布', 'stay_time' => '60 分鐘'],
                    ],
                    '3' => [
                        ['name' => '竹山天梯', 'stay_time' => '90 分鐘'],
                        ['name' => '竹山鎮老街', 'stay_time' => '60 分鐘'],
                    ],
                ],
                'chat_history' => [],
            ]);

            Trip::create([
                'user_id' => $lm->id,
                'title' => '合歡山賞雪之旅',
                'itinerary_data' => [
                    '1' => [
                        ['name' => '合歡山主峰', 'stay_time' => '120 分鐘'],
                        ['name' => '合歡山東峰', 'stay_time' => '120 分鐘'],
                        ['name' => '武嶺', 'stay_time' => '60 分鐘'],
                    ],
                    '2' => [
                        ['name' => '清境農場青青草原', 'stay_time' => '180 分鐘'],
                        ['name' => '清境老英格蘭莊園', 'stay_time' => '90 分鐘'],
                        ['name' => '清境小瑞士花園', 'stay_time' => '60 分鐘'],
                    ],
                ],
                'chat_history' => [],
            ]);

            Trip::create([
                'user_id' => $lm->id,
                'title' => '阿里山日出行',
                'itinerary_data' => [
                    '1' => [
                        ['name' => '阿里山森林遊樂區', 'stay_time' => '180 分鐘'],
                        ['name' => '阿里山神木群', 'stay_time' => '90 分鐘'],
                        ['name' => '阿里山姊妹潭', 'stay_time' => '60 分鐘'],
                    ],
                    '2' => [
                        ['name' => '阿里山日出觀景台', 'stay_time' => '90 分鐘'],
                        ['name' => '奮起湖老街', 'stay_time' => '60 分鐘'],
                    ],
                ],
                'chat_history' => [],
            ]);
        }

        // 海岸旅人
        $ls = User::firstOrCreate(
            ['email' => 'ls@gmail.com'],
            ['name' => '海岸旅人', 'password' => Hash::make('demo1234')]
        );

        if (Trip::where('user_id', $ls->id)->doesntExist()) {
            Trip::create([
                'user_id' => $ls->id,
                'title' => '墾丁海岸三日遊',
                'itinerary_data' => [
                    '1' => [
                        ['name' => '墾丁大街', 'stay_time' => '90 分鐘'],
                        ['name' => '墾丁南灣海灘', 'stay_time' => '150 分鐘'],
                        ['name' => '貓鼻頭公園', 'stay_time' => '60 分鐘'],
                    ],
                    '2' => [
                        ['name' => '龍磐公園', 'stay_time' => '60 分鐘'],
                        ['name' => '鵝鑾鼻燈塔', 'stay_time' => '60 分鐘'],
                        ['name' => '後壁湖漁港', 'stay_time' => '90 分鐘'],
                    ],
                    '3' => [
                        ['name' => '小灣海灘', 'stay_time' => '120 分鐘'],
                        ['name' => '船帆石', 'stay_time' => '45 分鐘'],
                    ],
                ],
                'chat_history' => [],
            ]);

            Trip::create([
                'user_id' => $ls->id,
                'title' => '北台灣寺廟巡禮',
                'itinerary_data' => [
                    '1' => [
                        ['name' => '行天宮', 'stay_time' => '60 分鐘'],
                        ['name' => '艋舺龍山寺', 'stay_time' => '60 分鐘'],
                        ['name' => '指南宮', 'stay_time' => '90 分鐘'],
                    ],
                    '2' => [
                        ['name' => '淡水鄞山寺', 'stay_time' => '45 分鐘'],
                        ['name' => '淡水漁人碼頭', 'stay_time' => '90 分鐘'],
                        ['name' => '淡水老街', 'stay_time' => '90 分鐘'],
                    ],
                ],
                'chat_history' => [],
            ]);

            Trip::create([
                'user_id' => $ls->id,
                'title' => '東海岸花東縱谷',
                'itinerary_data' => [
                    '1' => [
                        ['name' => '花蓮七星潭', 'stay_time' => '90 分鐘'],
                        ['name' => '石梯坪', 'stay_time' => '60 分鐘'],
                        ['name' => '磯崎海灘', 'stay_time' => '90 分鐘'],
                    ],
                    '2' => [
                        ['name' => '台東三仙台', 'stay_time' => '90 分鐘'],
                        ['name' => '台東小野柳', 'stay_time' => '60 分鐘'],
                        ['name' => '富岡漁港', 'stay_time' => '45 分鐘'],
                    ],
                    '3' => [
                        ['name' => '宜蘭南方澳漁港', 'stay_time' => '60 分鐘'],
                        ['name' => '宜蘭外澳沙灘', 'stay_time' => '90 分鐘'],
                        ['name' => '南天宮媽祖廟', 'stay_time' => '45 分鐘'],
                    ],
                ],
                'chat_history' => [],
            ]);
        }

        $this->command->info('PresentationSeeder 完成！lm@gmail.com（山岳）& ls@gmail.com（海岸），密碼皆為 demo1234。');
    }
}
