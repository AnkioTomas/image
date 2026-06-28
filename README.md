<p align="center">
  <img src="src/app/static/icons/android-chrome-192x192.png" width="96" alt="Logo">
</p>

# Ankioの图床

基于 Nova 框架的轻量图床服务，使用 WebDAV 作为存储后端。

## 功能

- 图片上传与管理（卡片式浏览）
- WebDAV 远程存储
- 外部上传 API（Token 鉴权，兼容 curl / ShareX / PicGo 等工具）
- 图片去重（基于文件哈希）
- 用户登录与权限管理

## 部署

### 环境要求

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10.3+

### 配置

```bash
cp src/example.config.php src/config.php
```

编辑 `src/config.php`，填写数据库连接信息。

### 运行

```bash
php nova.phar start
```

默认监听 `127.0.0.1:10211`。

## 外部上传 API

在管理后台「存储配置」页面获取 API Token 后即可使用。

### 方式一：multipart 文件上传

```bash
curl -X POST "https://your-domain/api/upload?auth=your-token" \
  -F "file=@image.png"
```

或通过 Header 传递 Token：

```bash
curl -X POST "https://your-domain/api/upload" \
  -H "Authorization: your-token" \
  -F "file=@image.png"
```

### 方式二：JSON Base64 上传

```bash
curl -X POST "https://your-domain/api/upload?auth=your-token" \
  -H "Content-Type: application/json" \
  -d '{"file": "base64编码内容"}'
```

### 返回

```json
{"code": 200, "msg": "上传成功", "data": "https://your-domain/i/hash"}
```

## 目录结构

```
src/
├── app/
│   ├── controller/     # 控制器
│   ├── database/       # 数据模型与 DAO
│   ├── storage/        # 存储后端实现
│   ├── static/         # 前端资源
│   └── view/           # 模板
├── nova/               # 框架与插件
├── config.php          # 运行配置（不入库）
└── example.config.php  # 配置模板
```

## License

MIT
