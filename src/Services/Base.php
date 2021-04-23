<?php

namespace Lackone\LaravelGatewayWorker\Services;

class Base
{
    /**
     * 服务名
     * @var string
     */
    public $service_name = '';

    public function __construct($service_name)
    {
        $this->service_name = $service_name;
    }

    /**
     * 启动服务
     */
    public function start()
    {

    }

    /**
     * 获取配置
     */
    public function config($name, $default = null)
    {
        return config("gw.{$this->service_name}.{$name}", $default);
    }
}