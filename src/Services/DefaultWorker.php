<?php

namespace Lackone\LaravelGatewayWorker\Services;

use Workerman\Worker;
use GatewayWorker\BusinessWorker;
use Lackone\LaravelGatewayWorker\Events\DefaultEvent;

class DefaultWorker extends Base
{
    public function start()
    {
        $worker = new BusinessWorker();
        $worker->service_name = $this->service_name;
        $worker->name = $this->config('worker.name', 'worker');
        $worker->count = $this->config('worker.count', 4);
        $worker->registerAddress = $this->config('register_address', '127.0.0.1:10001');
        $worker->eventHandler = $this->config('worker.event_handler', DefaultEvent::class);

        $worker->onWorkerStart = [$this, 'onWorkerStart'];
        $worker->onWorkerStop = [$this, 'onWorkerStop'];

        if (!defined('GLOBAL_START')) {
            $pid_file = $this->config('worker.pid_file');
            $log_file = $this->config('worker.log_file');
            $stdout_file = $this->config('worker.stdout_file');

            $pid_file && Worker::$pidFile = $pid_file;
            $log_file && Worker::$logFile = $log_file;
            $stdout_file && Worker::$stdoutFile = $stdout_file;

            Worker::runAll();
        }
    }

    public function onWorkerStart($worker)
    {
    }

    public function onWorkerStop($worker)
    {
    }
}