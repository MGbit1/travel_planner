<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TripFlow 覓路') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg">

        <meta property="og:title" content="{{ config('app.name', 'TripFlow 覓路') }} — 智慧旅遊規劃平台" />
        <meta property="og:description" content="用 AI 規劃完美行程、探索全球景點靈感，與旅人社群分享你的旅遊故事。" />
        <meta property="og:image" content="https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1200&q=80" />
        <meta property="og:url" content="{{ url()->current() }}" />
        <meta property="og:type" content="website" />
        <meta property="og:site_name" content="TripFlow 覓路" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" content="{{ config('app.name', 'TripFlow 覓路') }} — 智慧旅遊規劃平台" />
        <meta name="twitter:description" content="用 AI 規劃完美行程、探索全球景點靈感，與旅人社群分享你的旅遊故事。" />
        <meta name="twitter:image" content="https://images.unsplash.com/photo-1488085061387-422e29b40080?w=1200&q=80" />

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @stack('styles')

        <style>
            body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif; }
            ::-webkit-scrollbar { width: 6px; height: 6px; }
            ::-webkit-scrollbar-track { background: transparent; }
            ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
            ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        </style>
    </head>
    
    <body class="font-sans antialiased bg-slate-50 text-slate-800 selection:bg-indigo-100 selection:text-indigo-900">

        {{-- TripFlow 啟動 Loading 畫面 --}}
        <div id="tf-loader" style="position:fixed;inset:0;z-index:9999;background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#0f172a 100%);display:flex;flex-direction:column;align-items:center;justify-content:center;transition:opacity .5s ease">
            <div style="position:relative;width:72px;height:72px;margin-bottom:22px">
                <div style="position:absolute;inset:0;border-radius:50%;border:3px solid rgba(129,140,248,.15);border-top-color:#818cf8;animation:tfspin .85s linear infinite"></div>
                <div style="position:absolute;inset:9px;border-radius:50%;border:3px solid rgba(165,180,252,.1);border-bottom-color:#a5b4fc;animation:tfspin 1.3s linear infinite reverse"></div>
                <div style="position:absolute;inset:0;display:flex;align-items:center;justify-content:center">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5" stroke="#c7d2fe" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
            </div>
            <div style="color:#f1f5f9;font-family:'Inter',sans-serif;font-weight:800;font-size:20px;letter-spacing:-.03em;margin-bottom:6px">TripFlow<span style="color:#818cf8">.</span></div>
            <div style="color:#64748b;font-family:'Inter',sans-serif;font-size:12px;letter-spacing:.05em;text-transform:uppercase;font-weight:600">規劃你的完美旅程</div>
            <div style="margin-top:28px;width:140px;height:2px;background:rgba(255,255,255,.06);border-radius:999px;overflow:hidden">
                <div style="height:100%;width:35%;background:linear-gradient(90deg,#4f46e5,#818cf8,#4f46e5);border-radius:999px;animation:tfbar 1.8s ease-in-out infinite;background-size:200% 100%"></div>
            </div>
        </div>
        <style>
            @keyframes tfspin { to { transform: rotate(360deg); } }
            @keyframes tfbar { 0%{transform:translateX(-100%)} 100%{transform:translateX(420%)} }
        </style>
        <script>
            (function(){
                function hideLoader(){
                    var l=document.getElementById('tf-loader');
                    if(l){ l.style.opacity='0'; setTimeout(function(){ l.style.display='none'; },500); }
                }
                if(document.readyState==='complete'){ setTimeout(hideLoader,100); }
                else { window.addEventListener('load', function(){ setTimeout(hideLoader,100); }); }
            })();
        </script>

        <div class="min-h-screen flex flex-col">
            
            @include('layouts.navigation')

            @if (isset($header))
                <header class="bg-white/50 backdrop-blur-sm border-b border-slate-200 sticky top-16 z-40">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <main class="flex-1 w-full">
                {{ $slot }}
            </main>

        </div>
        @stack('scripts')
    </body>
</html>