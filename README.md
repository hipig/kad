# Kad-IM

此项目为 kad-im 的后端 API，基于 PHP 框架 [Laravel 10](https://learnku.com/docs/laravel/10.x) 开发。

## 运行环境要求

- Nginx 1.22+
- PHP 8.1+
- MySQL 5.7+
- Redis 5.0+

## 开发环境部署/安装

### 基础安装

#### 克隆源代码

克隆源代码到本地：

```
git clone https://gitee.com/pigzzz/kad.git
cd kad
```  

#### 安装扩展包依赖

```
composer install
```  

#### 生成配置文件

```
cp .env.example .env
```

你可以根据情况修改 `.env` 文件里的内容，如数据库连接、缓存、日志、队列设置等：

```
# URL
APP_URL=http://kad.test
...
# 日志
LOG_CHANNEL=daily
...
# 数据库
DB_HOST=localhost
DB_DATABASE=kad
DB_USERNAME=root
DB_PASSWORD=root
...
# 队列
QUEUE_CONNECTION=redis
...
# API
VITE_API_BASE_URL=/api/
VITE_ADMIN_API_BASE_URL=/admin/api/
...
#Tencent IM
TENCENT_IM_DOMAIN="https://console.tim.qq.com"
TENCENT_IM_APPID="***123456789***"
TENCENT_IM_APP_SECRET="***724d62a3fedbd***"
TENCENT_IM_ADMINISTRATOR_USERID="administrator"
TENCENT_IM_ADMINISTRATOR_USERSIG="***eJwtjLEOgjAURf*ls8***"
```
#### 生成数据表及生成初始数据

生成菜单及角色

```
php artisan migrate
php artisan db:seed --class=MenuSeeder
php artisan db:seed --class=RoleSeeder
```

#### 生成秘钥

```
php artisan key:generate
```

#### Passport 初始化

```
php artisan passport:install
```

将生成的 password grant 对应的 id 与 secret 记录下来，用于配置 env 变量。

```
PASSPORT_PASSWORD_CLIENT_ID=
PASSPORT_PASSWORD_CLIENT_SECRET=
```

如果忘记了，可以去 `oauth_clients` 表中找。

### 线上部署

#### Supervisor配置

运行项目队列

```
[program:kad-queue]
command=php artisan queue:work --tries=3 --timeout=1800
directory=/www/wwwroot/kad/
autorestart=true
startsecs=3
startretries=3
user=root
priority=999
numprocs=1
process_name=%(program_name)s_%(process_num)02d
```

### 链接入口

* 接口地址：http://yourdomain.com/api
* 管理后台：http://yourdomain.com/admin

管理员账号使用以下命令行生成：

```
php artisan admin:create-user
```
