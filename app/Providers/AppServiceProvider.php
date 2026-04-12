<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 💡 助教終極版：自動判斷來源！
        // 如果不是在終端機執行指令，且來源包含了 ngrok，才強制轉換網址
        if (!app()->runningInConsole()) {
            $host = request()->header('x-forwarded-host');
            if ($host && str_contains($host, 'ngrok')) {
                URL::forceRootUrl('https://' . $host);
                URL::forceScheme('https');
            }
        }
    }
}