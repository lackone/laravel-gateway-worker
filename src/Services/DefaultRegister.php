<?php

namespace Lackone\LaravelGatewayWorker\Services;

use Workerman\Worker;
use GatewayWorker\Register;

class DefaultRegister extends Base
{
    public function start()
    {
        $register = new Register($this->config('register.url', 'text://0.0.0.0:10001'));
        $register->name = $this->config('register.name', 'register');

        if (!defined('GLOBAL_START')) {
            $pid_file = $this->config('register.pid_file');
            $log_file = $this->config('register.log_file');
            $stdout_file = $this->config('register.stdout_file');

            $pid_file && Worker::$pidFile = $pid_file;
            $log_file && Worker::$logFile = $log_file;
            $stdout_file && Worker::$stdoutFile = $stdout_file;

            Worker::runAll();
        }
    }
}