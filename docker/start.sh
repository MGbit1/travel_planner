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

# 植入示範資料（含 idempotency 檢查，重複執行不會建立重複資料）
php artisan db:seed --class=DemoDataSeeder --force

# 清除並重建快取
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 設定 Nginx 監聽埠（Render 會設定 PORT 環境變數，預設 10000）
PORT=${PORT:-10000}
sed -i "s/listen 8000/listen $PORT/g" /etc/nginx/sites-available/default

# 啟動 PHP-FPM（背景）
php-fpm -D

# 啟動 Nginx（前景，保持容器運行）
nginx -g "daemon off;"
