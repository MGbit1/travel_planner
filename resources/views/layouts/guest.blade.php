<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AI 旅程大師') }} - 登入</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 antialiased bg-slate-50">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 relative overflow-hidden">
            
            <div class="absolute inset-0 z-0">
                <div class="absolute top-0 left-0 right-0 h-[450px] bg-gradient-to-r from-blue-600 to-indigo-700 transform -skew-y-6 -translate-y-24 shadow-2xl z-0"></div>
            </div>

            <div class="relative z-10 flex flex-col items-center w-full">
                <div class="mb-10 text-center animate-in fade-in slide-in-from-top-4 duration-500">
                    <a href="/" class="flex flex-col items-center gap-2 drop-shadow-md hover:scale-105 transition-transform">
                        <span class="text-6xl mb-2">🚀</span>
                        <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-wider drop-shadow-lg">AI 旅程大師</h1>
                        <p class="text-blue-200 text-xs font-bold tracking-widest uppercase mt-1">Multi-Day Smart Planning</p>
                    </a>
                </div>

                <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-2xl overflow-hidden sm:rounded-3xl border border-slate-100 animate-in fade-in zoom-in-95 duration-500">
                    {{ $slot }}
                </div>
                
                <div class="mt-8 relative z-10">
                    <a href="/" class="text-sm font-bold text-slate-400 hover:text-indigo-600 transition flex items-center gap-1.5 bg-white px-4 py-2 rounded-full shadow-sm border border-slate-200">
                        <span>⬅️</span> 返回地圖首頁
                    </a>
                </div>
            </div>
        </div>
    </body>
</html>