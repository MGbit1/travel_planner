<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'TripFlow') }} - 帳號登入與註冊</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            body { font-family: 'Inter', ui-sans-serif, system-ui, -apple-system, sans-serif; }
        </style>
    </head>
    <body class="font-sans text-slate-800 antialiased bg-slate-50 selection:bg-indigo-100 selection:text-indigo-900">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-10 sm:pt-0 relative overflow-hidden">
            
            <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
                <div class="absolute -top-[20%] -left-[10%] w-[50%] h-[50%] rounded-full bg-indigo-100/40 blur-3xl"></div>
                <div class="absolute top-[60%] -right-[10%] w-[40%] h-[60%] rounded-full bg-blue-50/60 blur-3xl"></div>
            </div>

            <div class="mb-8 z-10 text-center animate-in fade-in slide-in-from-bottom-4 duration-500">
                <a href="/" class="block hover:opacity-80 transition-opacity">
                    <h1 class="text-4xl font-extrabold tracking-tight text-slate-800">TripFlow<span class="text-indigo-600">.</span></h1>
                    <p class="text-slate-400 text-[11px] font-bold tracking-[0.2em] mt-1.5 uppercase">Smart Planning</p>
                </a>
            </div>

            <div class="w-full sm:max-w-md px-8 py-10 bg-white/80 backdrop-blur-xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-white/50 overflow-hidden sm:rounded-[2rem] z-10 animate-in fade-in zoom-in-95 duration-500 delay-150">
                {{ $slot }}
            </div>
            
            <div class="mt-8 z-10 animate-in fade-in duration-500 delay-300">
                <a href="/" class="text-sm font-semibold text-slate-400 hover:text-indigo-600 transition-colors flex items-center gap-1.5 bg-white/50 px-4 py-2 rounded-full border border-slate-200/50 shadow-sm backdrop-blur-sm">
                    <i class="bi bi-arrow-left"></i> 返回探索地圖
                </a>
            </div>
        </div>
    </body>
</html>