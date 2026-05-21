#!/bin/bash
set -e

cd /app

# 產生 APP_KEY（若尚未設定）
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# 建立 storage 連結
php artisan storage:link --force 2>/dev/null || true

# 執行資料庫遷移
php artisan migrate --force

# 清除並重建快取
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 啟動 PHP-FPM（背景）
php-fpm -D

# 啟動 Nginx（前景，保持容器運行）
nginx -g "daemon off;"
