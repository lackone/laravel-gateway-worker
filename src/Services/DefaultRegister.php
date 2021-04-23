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
            Worker::runAll();
        }
    }
}