# 前后端分离的商城
基于Laravel8.x,前端用的是uni-app,后台管理系统是Laravel-admin

# 安装
### 基本环境
- PHP 7.4+
- Elasticsearch

下载composer依赖
```
composer install
```
创建配置文件
```
cp .env.example .env
```
```
php artisan key:generate
```
```
php artisan jwt:secret
```
执行数据库迁移
```
php artisan migrate
```
生成假数据
```
php artisan db:seed
```
创建后台账号
```
php artisan admin:create-user
```
Elasticsearch 的迁移命令
```
php artisan es:migrate
```
同步商品数据到 Elasticsearch
```
php artisan es:sync-products
```
# 前端代码
[mall](https://github.com/kang013/mall/tree/master)

# 网页版
[H5端演示](http://shop.zq525.cn)

注册手机验证码是1234

# 后台
[后台演示](http://laravel-shop.zq525.cn/admin)

账户：test
密码：test

# 前端页面下载
[H5端页面下载](https://github.com/kang013/shop/releases/tag/1.0)

本地端口为127.0.0.1:8000
