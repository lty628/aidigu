# Docker 部署说明

本项目已配置 Docker 支持，包含 ThinkPHP 5.1 + Swoole WebSocket 聊天服务 + MySQL + Redis + Nginx 完整环境。

## 架构说明

项目使用 **单容器多进程** 架构，通过 **Supervisor** 在一个容器中同时运行：
- **PHP-FPM**: 处理 Web 请求
- **Swoole WebSocket Server**: 处理聊天服务 (端口 9501)

## 快速开始

### 1. 构建并启动容器

```bash
# 使用 docker-compose 构建并启动所有服务
docker-compose up -d --build
```

### 2. 配置环境变量

复制 `example_env` 为 `.env` 并修改配置：

```bash
cp example_env .env
```

修改 `.env` 中的数据库配置：
```ini
[database]
    type = mysql
    hostname = mysql  # Docker 容器中使用服务名
    database = aidigu
    username = aidigu
    password = aidigu123
    hostport = 3306

[app]
    chatSocketDomain = ws://localhost:9501/  # WebSocket 聊天服务地址
```

### 3. 初始化数据库

数据库会在首次启动时自动导入 `aidigu.sql`。

### 4. 访问应用

- **Web 应用**: http://localhost
- **WebSocket 聊天服务**: ws://localhost:9501
- **MySQL**: localhost:3306
- **Redis**: localhost:6379

## 容器管理命令

```bash
# 启动所有服务
docker-compose up -d

# 停止所有服务
docker-compose down

# 查看日志
docker-compose logs -f

# 查看特定服务日志
docker-compose logs -f app

# 查看 Swoole 聊天服务日志
docker-compose exec app tail -f /var/log/supervisor/swoole-chat.log

# 查看 Swoole 错误日志
docker-compose exec app tail -f /var/log/supervisor/swoole-chat-error.log

# 重启服务
docker-compose restart app

# 进入 PHP 容器
docker-compose exec app bash

# 进入 MySQL 容器
docker-compose exec mysql mysql -uroot -proot

# 执行 Composer 命令
docker-compose exec app composer install

# 清理所有容器和数据卷
docker-compose down -v
```

## Supervisor 进程管理

在容器内可以使用 supervisorctl 管理进程：

```bash
# 进入容器
docker-compose exec app bash

# 查看所有进程状态
supervisorctl status

# 重启 Swoole 聊天服务
supervisorctl restart swoole-chat

# 停止 Swoole 聊天服务
supervisorctl stop swoole-chat

# 启动 Swoole 聊天服务
supervisorctl start swoole-chat

# 重启 PHP-FPM
supervisorctl restart php-fpm

# 查看实时日志
supervisorctl tail -f swoole-chat
```

## 进程配置

### Supervisor 配置文件: `supervisord.conf`

```ini
[program:php-fpm]
command=/usr/local/sbin/php-fpm -F
autostart=true
autorestart=true

[program:swoole-chat]
command=/usr/local/bin/php /var/www/html/think chatserver 8 9501 0.0.0.0
autostart=true
autorestart=true
```

**命令参数说明**:
- `8`: worker_num (工作进程数)
- `9501`: 端口
- `0.0.0.0`: IP 地址 (监听所有网络接口)

### 修改 Swoole 服务配置

编辑 `supervisord.conf` 中的 `[program:swoole-chat]` 部分：

```ini
# 修改进程数和端口
command=/usr/local/bin/php /var/www/html/think chatserver 16 9502 0.0.0.0
```

然后重新构建容器：
```bash
docker-compose up -d --build
```

## 仅使用 Dockerfile（不使用 docker-compose）

如果只想构建单个 PHP 应用容器：

```bash
# 构建镜像
docker build -t aidigu-app .

# 运行容器
docker run -d \
  -p 9000:9000 \
  -p 9501:9501 \
  -v $(pwd):/var/www/html \
  --name aidigu_app \
  aidigu-app
```

## Nginx 配置

Nginx 配置文件位于 `default.conf`，已自动挂载到容器中。

## WebSocket 客户端连接

前端连接 WebSocket 示例：

```javascript
// 开发环境
const ws = new WebSocket('ws://localhost:9501');

// 生产环境（请修改为实际域名）
const ws = new WebSocket('ws://your-domain.com:9501');

ws.onopen = function() {
    console.log('WebSocket 连接已建立');
};

ws.onmessage = function(event) {
    console.log('收到消息:', event.data);
};
```

## 目录权限

容器会自动设置以下目录权限：
- `runtime/` - 运行时缓存目录 (777)
- `public/uploads/` - 文件上传目录 (777)

## 注意事项

1. **首次启动**: 首次启动会安装 Composer 依赖，可能需要较长时间
2. **数据持久化**: MySQL 数据存储在 Docker 卷 `mysql_data` 中
3. **生产环境**: 
   - 修改 MySQL 密码和其他敏感配置
   - 使用反向代理处理 WebSocket 连接（Nginx/Caddy）
   - 考虑使用 SSL/TLS 加密 WebSocket (wss://)
4. **Swoole 版本**: 默认安装 Swoole 4.8.12，可根据需要调整
5. **PHP 版本**: 默认使用 PHP 7.4，支持 ThinkPHP 5.1
6. **进程监控**: Supervisor 会自动重启崩溃的进程
7. **WebSocket 端口**: 默认 9501，需要在防火墙中开放

## 生产环境 WebSocket 代理配置

### Nginx 反向代理 WebSocket

在 `default.conf` 中添加：

```nginx
# WebSocket 代理
map $http_upgrade $connection_upgrade {
    default upgrade;
    '' close;
}

server {
    listen 80;
    server_name your-domain.com;

    # WebSocket 路由
    location /ws/ {
        proxy_pass http://app:9501;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection $connection_upgrade;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_read_timeout 600s;
    }

    # Web 应用路由
    location / {
        root /var/www/html/public;
        try_files $uri $uri/ /index.php?$query_string;
        
        location ~ \.php$ {
            fastcgi_pass app:9000;
            fastcgi_index index.php;
            fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
            include fastcgi_params;
        }
    }
}
```

## 自定义配置

### 修改 PHP 版本

编辑 `Dockerfile` 第一行：
```dockerfile
FROM php:8.0-fpm  # 改为需要的版本
```

### 修改 Swoole 版本

编辑 `Dockerfile` 中的 pecl install 行：
```dockerfile
RUN pecl install swoole-5.0.0  # 改为需要的版本
```

### 添加其他 PHP 扩展

在 `Dockerfile` 中添加：
```dockerfile
RUN docker-php-ext-install 扩展名
```

## 故障排查

### 1. 查看容器状态
```bash
docker-compose ps
```

### 2. 查看所有日志
```bash
docker-compose logs app
```

### 3. 检查 Swoole 服务是否运行
```bash
docker-compose exec app supervisorctl status swoole-chat
```

### 4. 测试 WebSocket 连接
```bash
# 使用 wscat 工具测试
npm install -g wscat
wscat -c ws://localhost:9501
```

### 5. 检查端口监听
```bash
docker-compose exec app netstat -tlnp | grep 9501
```

### 6. 重新构建（清除缓存）
```bash
docker-compose down
docker-compose build --no-cache
docker-compose up -d
```

### 7. Swoole 服务无法启动
```bash
# 查看详细错误日志
docker-compose exec app cat /var/log/supervisor/swoole-chat-error.log

# 手动测试启动
docker-compose exec app php /var/www/html/think chatserver 8 9501 0.0.0.0
```

## 性能优化建议

1. **调整 Worker 进程数**: 根据 CPU 核心数调整 Swoole worker_num
2. **开启 OPcache**: 在生产环境启用 PHP OPcache
3. **使用 Redis 会话**: 配置 ThinkPHP 使用 Redis 存储 Session
4. **数据库连接池**: Swoole 协程环境下使用连接池
