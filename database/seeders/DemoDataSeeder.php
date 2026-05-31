<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. 建立示範使用者 ──────────────────────────────────
        $userData = [
            ['name' => '王小明', 'email' => 'user01@demo.com'],
            ['name' => '林美玲', 'email' => 'user02@demo.com'],
            ['name' => '陳志豪', 'email' => 'user03@demo.com'],
            ['name' => '張雅婷', 'email' => 'user04@demo.com'],
            ['name' => '李建宏', 'email' => 'user05@demo.com'],
            ['name' => '黃淑芬', 'email' => 'user06@demo.com'],
            ['name' => '吳俊賢', 'email' => 'user07@demo.com'],
            ['name' => '劉雅雯', 'email' => 'user08@demo.com'],
            ['name' => '蔡宗翰', 'email' => 'user09@demo.com'],
            ['name' => '鄭秀珍', 'email' => 'user10@demo.com'],
        ];

        $users = collect();
        foreach ($userData as $u) {
            $users->push(User::firstOrCreate(
                ['email' => $u['email']],
                ['name' => $u['name'], 'password' => Hash::make('password123')]
            ));
        }

        // ── 2. 行程資料（真實台灣景點 + 正確座標）──────────────
        $tripTemplates = [
            // 台北
            [
                'title' => '台北三日文青之旅',
                'data'  => [
                    1 => [
                        ['name' => '國立故宮博物院',   'lat' => 25.1022, 'lng' => 121.5485, 'address' => '台北市士林區至善路二段221號', 'stay_time' => '3小時', 'cost_estimate' => '$350', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1599706398924-0be3dd41c4ce?w=400&q=80'],
                        ['name' => '士林夜市',         'lat' => 25.0879, 'lng' => 121.5240, 'address' => '台北市士林區基河路101號',   'stay_time' => '2小時', 'cost_estimate' => '$300', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                        ['name' => '淡水老街',         'lat' => 25.1706, 'lng' => 121.4382, 'address' => '新北市淡水區中正路',         'stay_time' => '2小時', 'cost_estimate' => '$200', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1633283522688-17cddf2e7e6a?w=400&q=80'],
                    ],
                    2 => [
                        ['name' => '象山步道',         'lat' => 25.0270, 'lng' => 121.5768, 'address' => '台北市信義區信義路五段150巷',  'stay_time' => '2小時', 'cost_estimate' => '$0',   'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1598935898639-81586f7d2129?w=400&q=80'],
                        ['name' => '台北101',          'lat' => 25.0338, 'lng' => 121.5645, 'address' => '台北市信義區信義路五段7號',   'stay_time' => '2小時', 'cost_estimate' => '$600', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1470173274384-c4e8e2f9ea7c?w=400&q=80'],
                        ['name' => '饒河夜市',         'lat' => 25.0506, 'lng' => 121.5776, 'address' => '台北市松山區八德路四段775號', 'stay_time' => '1.5小時', 'cost_estimate' => '$250', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                    ],
                    3 => [
                        ['name' => '九份老街',         'lat' => 25.1093, 'lng' => 121.8438, 'address' => '新北市瑞芳區基山街',         'stay_time' => '3小時', 'cost_estimate' => '$300', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1512361436605-a484bdb34b5f?w=400&q=80'],
                        ['name' => '平溪天燈老街',     'lat' => 25.0252, 'lng' => 121.7376, 'address' => '新北市平溪區平溪街',         'stay_time' => '2小時', 'cost_estimate' => '$200', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1519214605650-76a613ee3245?w=400&q=80'],
                    ],
                ],
            ],
            // 台中
            [
                'title' => '台中美食輕旅行',
                'data'  => [
                    1 => [
                        ['name' => '彩虹眷村',         'lat' => 24.1285, 'lng' => 120.6426, 'address' => '台中市南屯區春安路56巷',     'stay_time' => '1小時', 'cost_estimate' => '$0',   'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1579584425555-c3ce17fd4351?w=400&q=80'],
                        ['name' => '逢甲夜市',         'lat' => 24.1797, 'lng' => 120.6441, 'address' => '台中市西屯區文華路',         'stay_time' => '2小時', 'cost_estimate' => '$350', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                        ['name' => '宮原眼科',         'lat' => 24.1403, 'lng' => 120.6817, 'address' => '台中市中區中山路20號',       'stay_time' => '1小時', 'cost_estimate' => '$200', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1548366086-7f1b76106622?w=400&q=80'],
                    ],
                    2 => [
                        ['name' => '日月潭',           'lat' => 23.8609, 'lng' => 120.9119, 'address' => '南投縣魚池鄉中山路599號',   'stay_time' => '4小時', 'cost_estimate' => '$300', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1604608672516-5b0f9c5b6d5f?w=400&q=80'],
                        ['name' => '清境農場',         'lat' => 24.0833, 'lng' => 121.1667, 'address' => '南投縣仁愛鄉定遠新村170號', 'stay_time' => '3小時', 'cost_estimate' => '$500', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=400&q=80'],
                    ],
                ],
            ],
            // 高雄
            [
                'title' => '高雄港都悠閒兩日遊',
                'data'  => [
                    1 => [
                        ['name' => '駁二藝術特區',     'lat' => 22.6213, 'lng' => 120.2767, 'address' => '高雄市鹽埕區大勇路1號',     'stay_time' => '2小時', 'cost_estimate' => '$0',   'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1599706398924-0be3dd41c4ce?w=400&q=80'],
                        ['name' => '六合夜市',         'lat' => 22.6264, 'lng' => 120.3026, 'address' => '高雄市新興區六合二路',       'stay_time' => '2小時', 'cost_estimate' => '$350', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                        ['name' => '愛河',             'lat' => 22.6323, 'lng' => 120.2931, 'address' => '高雄市鹽埕區河西路',         'stay_time' => '1.5小時', 'cost_estimate' => '$0',   'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1568632234157-ce7aecd03d0d?w=400&q=80'],
                    ],
                    2 => [
                        ['name' => '佛光山',           'lat' => 22.7472, 'lng' => 120.4550, 'address' => '高雄市大樹區興田路153號',   'stay_time' => '3小時', 'cost_estimate' => '$0',   'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1608236415053-8574c8e3faeb?w=400&q=80'],
                        ['name' => '墾丁大街',         'lat' => 21.9449, 'lng' => 120.7955, 'address' => '屏東縣恆春鎮墾丁路',         'stay_time' => '2小時', 'cost_estimate' => '$300', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?w=400&q=80'],
                    ],
                ],
            ],
            // 宜蘭
            [
                'title' => '宜蘭兩天一夜輕旅',
                'data'  => [
                    1 => [
                        ['name' => '羅東夜市',         'lat' => 24.6773, 'lng' => 121.7697, 'address' => '宜蘭縣羅東鎮公正路與民生路口', 'stay_time' => '2小時', 'cost_estimate' => '$300', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                        ['name' => '梅花湖',           'lat' => 24.7128, 'lng' => 121.7943, 'address' => '宜蘭縣冬山鄉大埤路150號',   'stay_time' => '2小時', 'cost_estimate' => '$150', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80'],
                        ['name' => '冬山河親水公園',   'lat' => 24.6600, 'lng' => 121.7810, 'address' => '宜蘭縣五結鄉協和路',         'stay_time' => '1.5小時', 'cost_estimate' => '$0',   'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=400&q=80'],
                    ],
                    2 => [
                        ['name' => '頭城農場',         'lat' => 24.8567, 'lng' => 121.8201, 'address' => '宜蘭縣頭城鎮更新路167號',   'stay_time' => '3小時', 'cost_estimate' => '$500', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=400&q=80'],
                        ['name' => '礁溪溫泉',         'lat' => 24.8195, 'lng' => 121.7710, 'address' => '宜蘭縣礁溪鄉德陽路',         'stay_time' => '2小時', 'cost_estimate' => '$400', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1540555700478-4be289fbecef?w=400&q=80'],
                    ],
                ],
            ],
            // 花蓮
            [
                'title' => '花蓮太魯閣壯遊記',
                'data'  => [
                    1 => [
                        ['name' => '太魯閣國家公園',   'lat' => 24.1569, 'lng' => 121.6214, 'address' => '花蓮縣秀林鄉富世村富世291號', 'stay_time' => '4小時', 'cost_estimate' => '$0',   'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
                        ['name' => '七星潭',           'lat' => 24.0269, 'lng' => 121.6323, 'address' => '花蓮縣新城鄉七星街',         'stay_time' => '1.5小時', 'cost_estimate' => '$0',   'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400&q=80'],
                        ['name' => '東大門夜市',       'lat' => 23.9790, 'lng' => 121.6044, 'address' => '花蓮市自強夜市',             'stay_time' => '2小時', 'cost_estimate' => '$300', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                    ],
                    2 => [
                        ['name' => '鯉魚潭',           'lat' => 23.8917, 'lng' => 121.5544, 'address' => '花蓮縣壽豐鄉魚池村',         'stay_time' => '2小時', 'cost_estimate' => '$100', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80'],
                        ['name' => '雲山水夢幻湖',     'lat' => 23.8726, 'lng' => 121.5332, 'address' => '花蓮縣壽豐鄉',               'stay_time' => '2小時', 'cost_estimate' => '$200', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80'],
                    ],
                ],
            ],
            // 台南
            [
                'title' => '台南古都美食深度遊',
                'data'  => [
                    1 => [
                        ['name' => '赤崁樓',           'lat' => 23.0000, 'lng' => 120.2012, 'address' => '台南市中西區民族路二段212號', 'stay_time' => '1.5小時', 'cost_estimate' => '$100', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1568632234157-ce7aecd03d0d?w=400&q=80'],
                        ['name' => '安平古堡',         'lat' => 23.0008, 'lng' => 120.1618, 'address' => '台南市安平區國勝路82號',     'stay_time' => '1.5小時', 'cost_estimate' => '$80',  'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1568632234157-ce7aecd03d0d?w=400&q=80'],
                        ['name' => '花園夜市',         'lat' => 23.0220, 'lng' => 120.2280, 'address' => '台南市北區海安路',           'stay_time' => '2小時', 'cost_estimate' => '$350', 'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=400&q=80'],
                    ],
                    2 => [
                        ['name' => '奇美博物館',       'lat' => 22.9758, 'lng' => 120.2547, 'address' => '台南市仁德區文華路二段66號', 'stay_time' => '3小時', 'cost_estimate' => '$200', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1566159267722-c5cd97ba5940?w=400&q=80'],
                        ['name' => '四草綠色隧道',     'lat' => 23.0633, 'lng' => 120.1418, 'address' => '台南市安南區大眾路360號',   'stay_time' => '1.5小時', 'cost_estimate' => '$200', 'parking_available' => true,  'photo' => 'https://images.unsplash.com/photo-1500534314209-a25ddb2bd429?w=400&q=80'],
                    ],
                ],
            ],
            // 阿里山
            [
                'title' => '阿里山日出雲海之旅',
                'data'  => [
                    1 => [
                        ['name' => '阿里山國家森林遊樂區', 'lat' => 23.5142, 'lng' => 120.8031, 'address' => '嘉義縣阿里山鄉中正村7號', 'stay_time' => '4小時', 'cost_estimate' => '$300', 'parking_available' => true, 'photo' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80'],
                        ['name' => '祝山觀日台',       'lat' => 23.5081, 'lng' => 120.8126, 'address' => '嘉義縣阿里山鄉祝山',         'stay_time' => '2小時', 'cost_estimate' => '$0',   'parking_available' => false, 'photo' => 'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=400&q=80'],
                        ['name' => '奮起湖老街',       'lat' => 23.4862, 'lng' => 120.7217, 'address' => '嘉義縣竹崎鄉中和村',         'stay_time' => '1.5小時', 'cost_estimate' => '$150', 'parking_available' => true, 'photo' => 'https://images.unsplash.com/photo-1568632234157-ce7aecd03d0d?w=400&q=80'],
                    ],
                ],
            ],
        ];

        // ── 3. 為每位使用者建立 2–3 筆行程 ─────────────────────
        $trips = collect();
        $users->each(function (User $user, int $idx) use ($tripTemplates, &$trips) {
            $count = ($idx % 3 === 0) ? 3 : 2;
            $selected = array_slice($tripTemplates, ($idx * 2) % count($tripTemplates), $count, true);

            foreach ($selected as $tpl) {
                // 把模板資料轉成系統格式
                $itinerary = [];
                foreach ($tpl['data'] as $day => $places) {
                    $itinerary[$day] = array_map(fn($p) => [
                        'name'              => $p['name'],
                        'address'           => $p['address'],
                        'location'          => ['lat' => $p['lat'], 'lng' => $p['lng']],
                        'photo'             => $p['photo'],
                        'stay_time'         => $p['stay_time'],
                        'cost_estimate'     => $p['cost_estimate'],
                        'parking_available' => $p['parking_available'],
                        'rating'            => round(3.5 + mt_rand(0, 15) / 10, 1),
                    ], $places);
                }

                $trip = Trip::create([
                    'user_id'        => $user->id,
                    'title'          => $tpl['title'],
                    'itinerary_data' => $itinerary,
                    'chat_history'   => [],
                ]);

                $trips->push($trip);
            }
        });

        // ── 4. 社群貼文（每位使用者 1–2 篇） ───────────────────
        $postTemplates = [
            ['title' => '台北三日遊：從故宮到九份的完美路線', 'content' => '跟著這條路線走，完全不踩雷！故宮文物讓人驚嘆，九份夜晚的燈籠美得像千與千尋，強烈推薦！', 'days' => 3, 'views' => 420],
            ['title' => '宜蘭兩天一夜：梅花湖 + 羅東夜市超值推薦', 'content' => '梅花湖的倒影超療癒，晚上羅東夜市的三星蔥餅一定要吃！整個行程下來費用不超過3000元，CP值爆表。', 'days' => 2, 'views' => 316],
            ['title' => '高雄美食之旅：不踩雷的5個必去景點', 'content' => '駁二藝術特區適合拍照，六合夜市木瓜牛奶必喝，愛河夜景讓人心情超好，推薦給第一次來高雄的朋友！', 'days' => 2, 'views' => 289],
            ['title' => '台中一日遊攻略：文青 × 美食 × 網美打卡', 'content' => '彩虹眷村色彩超繽紛，宮原眼科的冰淇淋真的太好吃，逢甲夜市人雖多但值得！建議平日來避開人潮。', 'days' => 1, 'views' => 502],
            ['title' => '花蓮太魯閣兩日絕景之旅', 'content' => '太魯閣的峽谷震撼無比，七星潭的石頭讓人忘記時間，東大門夜市有很多原住民特色美食值得一試！', 'days' => 2, 'views' => 378],
            ['title' => '台南古都慢旅：赤崁樓 + 安平古堡深度散策', 'content' => '台南的歷史底蘊真的很深厚，走在這兩個古蹟之間彷彿穿越時空，晚上去花園夜市補一補，完美！', 'days' => 2, 'views' => 245],
            ['title' => '阿里山追雲海、看日出 — 一次滿足兩個願望', 'content' => '清晨4點出發爬祝山看日出，雲海就在腳下翻滾，那一刻真的覺得一切辛苦都值得，這輩子必去！', 'days' => 1, 'views' => 467],
            ['title' => '日月潭環湖慢騎一日遊', 'content' => '租借單車環湖大約3–4小時，湖景配上山嵐，空氣超級清新，推薦早上八點出發避開旅遊團！', 'days' => 1, 'views' => 334],
            ['title' => '九份 × 平溪 北部秘境兩日行', 'content' => '九份的雨天更有味道，平溪放天燈是這輩子難忘的體驗，回程記得買芋圓帶回家！', 'days' => 2, 'views' => 412],
            ['title' => '墾丁三天兩夜：海水浴場 + 夜市 + 浮潛', 'content' => '南灣的水超清澈，浮潛看到很多熱帶魚，墾丁大街的烤玉米和海鮮真的沒話說，夏天必來！', 'days' => 3, 'views' => 389],
            ['title' => '礁溪溫泉一日遊：泡湯 + 美食 + 放鬆', 'content' => '礁溪距台北只要1小時車程，泡完溫泉再去羅東吃蔥餅，下午就回台北，週末說走就走的好選擇！', 'days' => 1, 'views' => 271],
            ['title' => '清境農場 × 合歡山高山秘境之旅', 'content' => '清境的綿羊超可愛，合歡山上看到滿天星星讓人感動，但高山症要注意，建議多喝水休息。', 'days' => 2, 'views' => 358],
            ['title' => '台南奇美博物館 — 最奢華的免費美術館', 'content' => '奇美博物館真的是台灣最美博物館之一，館藏豐富，四草綠色隧道的紅樹林也很特別，推薦同一天一起去！', 'days' => 1, 'views' => 293],
            ['title' => '花蓮鯉魚潭 × 雲山水 療癒系輕旅行', 'content' => '兩個湖都各有特色，鯉魚潭適合划船，雲山水更安靜夢幻，適合想放空的旅人。', 'days' => 2, 'views' => 187],
            ['title' => '台北美食一日衝：饒河 + 士林 雙夜市挑戰', 'content' => '一天逛兩個夜市雖然挑戰肚子極限，但饒河的胡椒餅和士林的大雞排都是必吃！推薦給吃貨！', 'days' => 1, 'views' => 441],
        ];

        $posts = collect();
        $users->each(function (User $user, int $idx) use ($postTemplates, $trips, &$posts) {
            $count = ($idx % 2 === 0) ? 2 : 1;
            $userTrips = $trips->where('user_id', $user->id)->values();

            for ($i = 0; $i < $count; $i++) {
                $tplIdx  = ($idx + $i) % count($postTemplates);
                $tpl     = $postTemplates[$tplIdx];
                $trip    = $userTrips->get($i);

                $post = Post::create([
                    'user_id'     => $user->id,
                    'trip_id'     => $trip?->id,
                    'title'       => $tpl['title'],
                    'content'     => $tpl['content'],
                    'days_count'  => $tpl['days'],
                    'views_count' => $tpl['views'] + mt_rand(-30, 80),
                    'image_url'   => $trip
                        ? (collect($trip->itinerary_data[1] ?? [])->firstWhere('photo') ?? [])['photo'] ?? null
                        : null,
                ]);

                $posts->push($post);
            }
        });

        // ── 5. 按讚資料 ─────────────────────────────────────────
        $posts->each(function (Post $post) use ($users) {
            $likeCount  = mt_rand(3, 15);
            $shuffled   = $users->shuffle()->take($likeCount);
            foreach ($shuffled as $user) {
                PostLike::firstOrCreate([
                    'post_id' => $post->id,
                    'user_id' => $user->id,
                ]);
            }
        });

        // ── 6. 留言資料 ─────────────────────────────────────────
        $commentPool = [
            '謝謝分享！我下週就要去了！',
            '請問停車方便嗎？',
            '照這個路線走真的很順！推推！',
            '我去年有去，超推！一定要早點出發避開人潮',
            '這篇收藏了，下次假期就用這個行程！',
            '請問有適合帶小孩的景點嗎？',
            '交通方式有推薦搭大眾運輸嗎？',
            '太厲害了！這一天能走完真的嗎？',
            '照片好美，讓我馬上訂機票的衝動！',
            '費用統計很實用，謝謝你的整理！',
            '我也去過，完全同意！真的很值得！',
            '請問住宿有推薦嗎？',
            '行程超完整！第一次去不知道怎麼玩可以直接參考這篇',
            '美食推薦有沒有補充？好想知道吃了什麼！',
            '看完這篇我決定周末就出發！',
        ];

        $posts->each(function (Post $post) use ($users, $commentPool) {
            $commentCount = mt_rand(2, 5);
            $shuffled     = $users->shuffle()->take($commentCount);
            $usedComments = [];

            foreach ($shuffled as $user) {
                do {
                    $comment = $commentPool[array_rand($commentPool)];
                } while (in_array($comment, $usedComments) && count($usedComments) < count($commentPool));

                $usedComments[] = $comment;
                Comment::create([
                    'user_id' => $user->id,
                    'post_id' => $post->id,
                    'content' => $comment,
                ]);
            }
        });

        $this->command->info('DemoDataSeeder 完成！建立了 ' . $users->count() . ' 位使用者、' . $trips->count() . ' 筆行程、' . $posts->count() . ' 篇貼文。');
    }
}
