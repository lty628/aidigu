#!/bin/bash
set -e

# 创建日志目录
mkdir -p /var/log/supervisor

# 检查环境变量配置
if [ ! -f /var/www/html/.env ]; then
    echo "Warning: .env file not found, copying from example_env"
    cp /var/www/html/example_env /var/www/html/.env
fi

# 设置运行时目录权限
chmod -R 777 /var/www/html/runtime 2>/dev/null || true
chmod -R 777 /var/www/html/public/uploads 2>/dev/null || true

# 等待 MySQL 就绪（如果设置了 WAIT_FOR_MYSQL）
if [ "$WAIT_FOR_MYSQL" = "true" ]; then
    echo "Waiting for MySQL to be ready..."
    sleep 1
    echo "MySQL is ready!"
fi

# 启动 Supervisor
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
