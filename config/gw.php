<?php

return [
    'service_name' => 'default',

    'default' => [
        'pid_file' => storage_path('logs/pid.log'),
        'log_file' => storage_path('logs/log.log'),
        'stdout_file' => storage_path('logs/stdout_file.log'),

        'register_address' => '127.0.0.1:10001',

        //worker进程负责处理实际的业务逻辑，并将结果推送给对应的客户端
        'worker' => [
            'handler' => \Lackone\LaravelGatewayWorker\Services\DefaultWorker::class,
            'name' => 'worker',
            'count' => 4,
            'event_handler' => \Lackone\LaravelGatewayWorker\Events\DefaultEvent::class,
            //如果设置了这个命名空间，则通过反射，调用onMessage中type指定的类方法
            //主要的目的是为了方便代码管理，后期扩展
            'event_namespace' => 'App\\Services',

            'pid_file' => storage_path('logs/worker_pid.log'),
            'log_file' => storage_path('logs/worker_log.log'),
            'stdout_file' => storage_path('logs/worker_stdout_file.log'),
        ],

        //gateway进程负责维持客户端连接，并转发客户端的数据给worker进程处理
        'gateway' => [
            'handler' => \Lackone\LaravelGatewayWorker\Services\DefaultGateway::class,
            'name' => 'gateway',
            'count' => 4,
            'start_port' => 11000,
            'url' => 'websocket://0.0.0.0:10002',
            //分布式部署时使用内网ip
            'lan_ip' => '127.0.0.1',
            'ping_interval' => 50,
            'ping_data' => '{"type":"ping"}',
            'ping_not_response_limit' => 0,
            'context' => [
                //'ssl' => [
                //    'local_cert' => '',
                //    'local_pk' => '',
                //    'verify_peer' => false,
                //],
            ],

            'pid_file' => storage_path('logs/gateway_pid.log'),
            'log_file' => storage_path('logs/gateway_log.log'),
            'stdout_file' => storage_path('logs/gateway_stdout_file.log'),
        ],

        //register用于协调集群内部gateway与worker的通信
        'register' => [
            'handler' => \Lackone\LaravelGatewayWorker\Services\DefaultRegister::class,
            'name' => 'register',
            //register只能定制监听的ip和端口，并且目前只能使用text协议
            'url' => 'text://0.0.0.0:10001',

            'pid_file' => storage_path('logs/register_pid.log'),
            'log_file' => storage_path('logs/register_log.log'),
            'stdout_file' => storage_path('logs/register_stdout_file.log'),
        ],
    ],
];