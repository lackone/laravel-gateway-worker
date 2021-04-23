<?php

namespace Lackone\LaravelGatewayWorker\Commands;

use Illuminate\Console\Command;
use Lackone\LaravelGatewayWorker\Exceptions\Exception;
use Workerman\Worker;

class GatewayWorkerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'gw {service_name} {server} {action} {--d}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start a GatewayWorker Service.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        global $argv;

        $service_name = $this->argument('service_name') ?: config('gw.service_name');
        $server = $this->argument('server') ?: 'all';
        $action = $this->argument('action') ?: 'start';

        if (!$service_name) {
            $this->error('service_name is empty');
        }

        if (!in_array($server, ['all', 'worker', 'gateway', 'register'])) {
            $this->error('server not in [all, worker, gateway, register]');
        }

        if (!in_array($action, ['status', 'start', 'stop', 'restart', 'reload', 'connections'])) {
            $this->error('action not in [status, start, stop, restart, reload, connections]');
        }

        $daemon = $this->option('d') ? '-d' : '';

        if ($server == 'all') {
            define('GLOBAL_START', 1);
            $server_list = ['worker', 'gateway', 'register'];
        } else {
            $server_list[] = $server;
        }

        $pid_file = config("gw.{$service_name}.pid_file");
        $log_file = config("gw.{$service_name}.log_file");
        $stdout_file = config("gw.{$service_name}.stdout_file");

        $pid_file && Worker::$pidFile = $pid_file;
        $log_file && Worker::$logFile = $log_file;
        $stdout_file && Worker::$stdoutFile = $stdout_file;

        foreach ($server_list as $value) {
            $class = config("gw.{$service_name}.{$value}.handler");

            $argv[0] = 'gw';
            $argv[1] = $action;
            $argv[2] = $daemon;

            $service = new $class($service_name);
            try {
                $service->start();
            } catch (Exception $e) {
                $this->error($e->getMessage());
            }
        }

        if ($server == 'all') {
            Worker::runAll();
        }
    }
}