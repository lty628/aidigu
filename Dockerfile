# 使用官方 PHP 7.4 FPM 镜像作为基础镜像
FROM php:7.4-fpm

# 设置工作目录
WORKDIR /var/www/html

# 安装系统依赖和 Supervisor
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    wget \
    libssl-dev \
    libcurl4-openssl-dev \
    supervisor \
    procps \
    dos2unix \
    && rm -rf /var/lib/apt/lists/*

# 安装 PHP 扩展
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    sockets

# 安装 Swoole 扩展
RUN pecl install swoole-4.8.12 \
    && docker-php-ext-enable swoole

# 安装 Redis 扩展（可选，常用于会话和缓存）
RUN pecl install redis-5.3.7 \
    && docker-php-ext-enable redis

# 安装 Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 复制启动脚本和配置文件
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
COPY supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# 转换 Windows 换行符为 Unix 格式并设置执行权限
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

# 复制项目文件
COPY . /var/www/html

# 设置权限
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && mkdir -p /var/www/html/runtime \
    && mkdir -p /var/www/html/public/uploads \
    && chmod -R 777 /var/www/html/runtime \
    && chmod -R 777 /var/www/html/public/uploads

# 安装项目依赖
RUN composer install --no-dev --optimize-autoloader --no-interaction

# 创建 .env 文件（如果不存在）
RUN if [ ! -f .env ]; then cp example_env .env 2>/dev/null || true; fi

# 暴露端口
# 9000: PHP-FPM
# 9501: Swoole WebSocket Server (聊天服务)
EXPOSE 9000 9501

# 使用 Supervisor 启动多个服务
ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
