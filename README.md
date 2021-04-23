# laravel-gateway-worker
为了在laravel中方便的使用GatewayWorker，于是基于GatewayWorker写了一个扩展。

## 一、安装

```
$ composer require lackone/laravel-gateway-worker
```

## 二、配置
1、在 config/app.php 注册 ServiceProvider (Laravel5.5+无需手动注册)

```
'providers' => [
    // ...
    Lackone\LaravelGatewayWorker\Providers\GatewayWorkerServiceProvider::class,
];
```

2、创建配置文件

```
php artisan vendor:publish --provider="Lackone\LaravelGatewayWorker\Providers\GatewayWorkerServiceProvider"
```

3、修改配置文件

根据需要修改 config/gw.php 中的配置即可 。

## 三、使用
创建一个用于处理消息的文件，比如 test.php ，存放目录随意，并设置event_namespace命名空间。

```
<?php

namespace App\Services;

use GatewayWorker\Lib\Gateway;

class Test
{
    public function test($client_id, $message)
    {
        Gateway::sendToClient($client_id, date('YmdHis'));
    }
}
```
前端websocket连接的地址，应该是gw.php配置文件中gateway.url的配置
```
<!doctype html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
<button id="btn">发送</button>
<script>
    document.getElementById("btn").onclick = function () {
        ws.send(JSON.stringify({"type": "test", "data": "test"}));
    };
    ws = new WebSocket("ws://192.168.10.223:10002");
    ws.onopen = function () {

    };
    ws.onmessage = function (e) {
        console.log(e.data);
    };
    ws.onclose = function () {
        console.log("连接关闭，定时重连");
    };
    ws.onerror = function () {
        console.log("出现错误");
    };
</script>
</body>
</html>
```

## 四、启动
```
php artisan gw default all start
```
config/gw.php 中可以配置多个配置项