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
        if (!app()->runningInConsole()) {
            $host = request()->header('x-forwarded-host');

            // ngrok 本地隧道
            if ($host && str_contains($host, 'ngrok')) {
                URL::forceRootUrl('https://' . $host);
                URL::forceScheme('https');
            }

            // 正式環境（Render 等雲端平台）強制 HTTPS
            if (config('app.env') !== 'local') {
                URL::forceScheme('https');
            }
        }
    }
}