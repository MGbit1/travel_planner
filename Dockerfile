FROM php:8.2-fpm

# 1. 先安裝 Debian 系統依賴套件（編譯 PHP 擴充必要的底層工具）
RUN apt-get update && apt-get install -y \
    git curl unzip nginx \
    libpq-dev \
    libonig-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    && rm -rf /var/lib/apt/lists/*

# 2. 接著再安心安裝 PHP 擴充套件（含 PostgreSQL）
RUN docker-php-ext-install \
    pdo pdo_pgsql pgsql \
    mbstring zip bcmath gd intl opcache

# 安裝 Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 安裝 Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# 複製專案
COPY . .

# 安裝 PHP 套件
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 打包前端資源
RUN npm ci && npm run build && rm -rf node_modules

# 設定權限
RUN chown -R www-data:www-data storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# 複製 Nginx 設定
COPY docker/nginx.conf /etc/nginx/sites-available/default

# 複製啟動腳本
COPY docker/start.sh /start.sh
RUN chmod +x /start.sh

EXPOSE 10000

CMD ["/start.sh"]
