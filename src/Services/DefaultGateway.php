<?php

namespace Lackone\LaravelGatewayWorker\Services;

use Workerman\Worker;
use GatewayWorker\Gateway;

class DefaultGateway extends Base
{
    public function start()
    {
        $gateway = new Gateway($this->config('gateway.url', 'websocket://0.0.0.0:10002'), $this->config('gateway.context', []));
        $gateway->name = $this->config('gateway.name', 'gateway');
        $gateway->count = $this->config('gateway.count', 4);
        $gateway->lanIp = $this->config('gateway.lan_ip', '127.0.0.1');
        $gateway->startPort = $this->config('gateway.start_port', 11000);
        $gateway->registerAddress = $this->config('register_address', '127.0.0.1:10001');
        $gateway->pingInterval = $this->config('gateway.ping_interval', 50);
        $gateway->pingData = $this->config('gateway.ping_data', '{"type":"ping"}');
        $gateway->pingNotResponseLimit = $this->config('gateway.ping_not_response_limit', 0);

        $gateway->onWorkerStart = [$this, 'onWorkerStart'];
        $gateway->onWorkerStop = [$this, 'onWorkerStop'];
        $gateway->onConnect = [$this, 'onConnect'];
        $gateway->onClose = [$this, 'onClose'];

        if (!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }

    public function onWorkerStart($worker)
    {
    }

    public function onConnect($connection)
    {
    }

    public function onClose($connection)
    {
    }

    public function onWorkerStop($worker)
    {
    }
}