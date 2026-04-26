<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\PostLike;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        // 1. 清空舊資料（順序：按讚 → 留言 → 貼文）
        PostLike::query()->delete();
        Comment::query()->delete();
        Post::query()->delete();

        // 2. 建立模擬用戶（若 email 已存在則沿用）
        $usersData = [
            ['name' => '旅行小妍',   'email' => 'yan_travel@example.com'],
            ['name' => '背包客阿明', 'email' => 'ming_backpack@example.com'],
            ['name' => '週末探險家', 'email' => 'weekend_explorer@example.com'],
            ['name' => '美食旅者',   'email' => 'foodie_travel@example.com'],
            ['name' => '島嶼追風者', 'email' => 'island_chaser@example.com'],
        ];

        $users = [];
        foreach ($usersData as $data) {
            $users[] = User::firstOrCreate(
                ['email' => $data['email']],
                ['name' => $data['name'], 'password' => Hash::make('password')]
            );
        }

        // 3. 假貼文資料（含 picsum 圖片 + 中文旅遊內容）
        $posts = [
            [
                'title'      => '九份老街兩日遊 夕陽紅燈籠美翻了',
                'content'    => '從台北搭客運直達九份，老街的紅燈籠配上夕陽真的超美！阿妹茶樓看出去的景色太震撼了，晚上再吃芋圓配豆花，整趟旅程非常值得推薦！',
                'days_count' => 2,
                'image_url'  => 'https://picsum.photos/seed/jiufen2024/800/500',
                'views'      => rand(300, 900),
            ],
            [
                'title'      => '京都五日深度遊 秋楓紅到心醉',
                'content'    => '嵐山竹林、金閣寺、伏見稻荷每個地方都美到捨不得走。早起去稻荷避開人潮，整個感覺非常不同。推薦住在祇園附近，走路就能逛！',
                'days_count' => 5,
                'image_url'  => 'https://picsum.photos/seed/kyoto2024/800/500',
                'views'      => rand(500, 1200),
            ],
            [
                'title'      => '墾丁三日遊 浮潛看魚超療癒',
                'content'    => '南灣的水超清，浮潛看到好多熱帶魚！晚上逛墾丁大街，烤玉米和新鮮椰子必買。住在青年活動中心，價格實惠景色又好，下次還要來！',
                'days_count' => 3,
                'image_url'  => 'https://picsum.photos/seed/kenting2024/800/500',
                'views'      => rand(200, 700),
            ],
            [
                'title'      => '日月潭單車環湖 一日輕旅行',
                'content'    => '騎腳踏車環湖大概 4 小時，沿途風景美不勝收。搭纜車上伊達邵吃飛魚卵香腸，晚上在水社看日落，整個人都充電完畢了！',
                'days_count' => 1,
                'image_url'  => 'https://picsum.photos/seed/sunmoon2024/800/500',
                'views'      => rand(150, 500),
            ],
            [
                'title'      => '大阪美食掃街四天三夜吃不停',
                'content'    => '道頓堀章魚燒、黑門市場海鮮、蟹道樂全都吃了！環球影城哈利波特園區超驚豔。心齋橋買了好多藥妝，行李超重回來搭計程車才塞得進去哈哈。',
                'days_count' => 4,
                'image_url'  => 'https://picsum.photos/seed/osaka2024/800/500',
                'views'      => rand(400, 1000),
            ],
            [
                'title'      => '花蓮太魯閣步道輕健行',
                'content'    => '錐麓古道真的很震撼，峭壁上的步道俯瞰立霧溪超美！砂卡礑步道適合全家，溪水清澈見底。晚上在市區吃公正包子和公園号炒米粉，太滿足了。',
                'days_count' => 2,
                'image_url'  => 'https://picsum.photos/seed/taroko2024/800/500',
                'views'      => rand(250, 800),
            ],
            [
                'title'      => '首爾五日追星之旅 弘大聖地巡禮',
                'content'    => 'SM 旗艦店、NCT 夢幻工廠都去了！弘大服飾超便宜。韓式炸雞加啤酒是首爾必備，明洞面膜一次買 30 片，整箱帶回來超值！',
                'days_count' => 5,
                'image_url'  => 'https://picsum.photos/seed/seoul2024/800/500',
                'views'      => rand(300, 900),
            ],
            [
                'title'      => '宜蘭礁溪溫泉泡湯一日放鬆',
                'content'    => '從台北坐火車 40 分鐘就到礁溪，泡完戶外碳酸泉整個身體都輕了！午餐吃羊肉爐，下午去傳藝中心買伴手禮，傍晚回台北剛好，超完美的一日遊！',
                'days_count' => 1,
                'image_url'  => 'https://picsum.photos/seed/yilan2024/800/500',
                'views'      => rand(100, 400),
            ],
            [
                'title'      => '清邁古城慢活四天 每間咖啡廳都打卡',
                'content'    => '騎摩托車繞古城，拜了七家寺廟。週日夜市手工藝品超多，買了竹編包和香皂。Nimman 路的咖啡廳一間比一間美，泰式按摩一小時才 200 泰銖，天天去！',
                'days_count' => 4,
                'image_url'  => 'https://picsum.photos/seed/chiangmai2024/800/500',
                'views'      => rand(200, 600),
            ],
            [
                'title'      => '台南古都三日漫遊 小吃吃不完',
                'content'    => '赤崁樓、安平古堡、神農街一日逛完。擔仔麵、牛肉湯、鱔魚意麵每頓必點。台南的咖啡廳文化很盛，坐一個下午都不無聊，是個讓人想住下來的城市。',
                'days_count' => 3,
                'image_url'  => 'https://picsum.photos/seed/tainan2024/800/500',
                'views'      => rand(350, 850),
            ],
            [
                'title'      => '北海道初雪之旅 函館夜景絕美',
                'content'    => '函館山纜車看夜景真的是日本三大夜景名不虛傳！朝市的海鮮丼料超多超新鮮。泡洞爺湖溫泉，窗外是白雪山景，這輩子必體驗一次的行程！',
                'days_count' => 5,
                'image_url'  => 'https://picsum.photos/seed/hokkaido2024/800/500',
                'views'      => rand(400, 1100),
            ],
            [
                'title'      => '阿里山森林鐵路 凌晨搶看日出雲海',
                'content'    => '凌晨四點起床搭小火車上祝山，雲海配上玉山超壯觀！神木群步道走起來很舒服，芬多精超充足。記得一定要帶厚外套，山上真的很冷！',
                'days_count' => 2,
                'image_url'  => 'https://picsum.photos/seed/alishan2024/800/500',
                'views'      => rand(300, 750),
            ],
            [
                'title'      => '沖繩海島度假四日 海水藍到不真實',
                'content'    => '美麗海水族館的鯨鯊超震撼！古宇利島的愛心岩超夢幻。居酒屋吃海葡萄和海鮮，喝泡盛酒，晚上逛國際通。整趟旅程完全放鬆，回台灣還在回味中。',
                'days_count' => 4,
                'image_url'  => 'https://picsum.photos/seed/okinawa2024/800/500',
                'views'      => rand(450, 1000),
            ],
            [
                'title'      => '高雄港都一日玩透透 夕陽最美',
                'content'    => '駁二特區、西子灣看夕陽、六合夜市一日完食！搭捷運超方便，西子灣的夕陽真的名不虛傳。愛河夜間遊船浪漫指數爆表，下次要帶另一半來！',
                'days_count' => 1,
                'image_url'  => 'https://picsum.photos/seed/kaohsiung2024/800/500',
                'views'      => rand(180, 550),
            ],
            [
                'title'      => '曼谷五天 寺廟美食購物一次滿足',
                'content'    => '大皇宮和臥佛寺一定要去，記得穿長褲長裙！恰圖恰週末市集買了超多小物。Tom Yum Kung 每天必吃，泰式按摩一天兩次不嫌多，是最享受的旅行之一！',
                'days_count' => 5,
                'image_url'  => 'https://picsum.photos/seed/bangkok2024/800/500',
                'views'      => rand(350, 900),
            ],
        ];

        // 4. 建立貼文並加上隨機按讚與留言
        $commentTexts = [
            '好羨慕！下次我也要去！',
            '這個景點我去過，真的超美的！',
            '請問住宿是哪裡訂的？',
            '照片好美，拍照技術很好！',
            '跟著你的攻略去絕對不會踩雷！',
            '謝謝分享，收藏起來準備去！',
            '感謝詳細的介紹，超實用！',
            '這樣的旅行好療癒，想跟著去！',
            '真的很值得去，吃的玩的都很讚！',
            '請問大概花了多少預算呢？',
        ];

        foreach ($posts as $data) {
            $author = $users[array_rand($users)];

            $post = Post::create([
                'user_id'     => $author->id,
                'trip_id'     => null,
                'title'       => $data['title'],
                'content'     => $data['content'],
                'image_url'   => $data['image_url'],
                'days_count'  => $data['days_count'],
                'views_count' => $data['views'],
            ]);

            // 隨機按讚（2~4 個不同用戶）
            $likeCount = rand(2, 4);
            $likers = collect($users)->shuffle()->take($likeCount);
            foreach ($likers as $liker) {
                if ($liker->id !== $author->id) {
                    PostLike::firstOrCreate([
                        'post_id' => $post->id,
                        'user_id' => $liker->id,
                    ]);
                }
            }

            // 隨機留言（1~3 則）
            $commentCount = rand(1, 3);
            $commenters = collect($users)->shuffle()->take($commentCount);
            foreach ($commenters as $commenter) {
                Comment::create([
                    'post_id' => $post->id,
                    'user_id' => $commenter->id,
                    'content' => $commentTexts[array_rand($commentTexts)],
                ]);
            }
        }
    }
}
