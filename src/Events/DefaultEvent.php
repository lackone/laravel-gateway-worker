<?php

namespace Lackone\LaravelGatewayWorker\Events;

//declare(ticks=1);

use GatewayWorker\Lib\Gateway;

class DefaultEvent
{
    public static $service_name = '';

    public static function onWorkerStart($worker)
    {
        self::$service_name = $worker->service_name;
    }

    /**
     * 当客户端连接时触发
     */
    public static function onConnect($client_id)
    {
        Gateway::sendToClient($client_id, json_encode([
            'type' => 'connect',
            'client_id' => $client_id,
        ]));
    }

    /**
     * 当客户端发来消息时触发
     */
    public static function onMessage($client_id, $message)
    {
        $message = json_decode($message, true) ?: $message;

        $namespace = config('gw.' . self::$service_name . '.worker.event_namespace');

        if ($namespace && $message['type']) {
            $class = '';
            $method = '';
            $type = preg_split('/[@|.\\-,\\\\\/]/', $message['type']);
            $type_count = count($type);
            switch ($type_count) {
                case 0:
                    break;
                case 1:
                    $class = $namespace . '\\' . ucfirst($type[0]);
                    $method = lcfirst($type[0]);
                    break;
                default:
                    $class = $namespace;
                    foreach ($type as $ix => $value) {
                        if ($ix == $type_count - 1) {
                            break;
                        }
                        $class .= '\\' . ucfirst($value);
                    }
                    $method = lcfirst($type[$type_count - 1]);
                    break;
            }
            if (class_exists($class)) {
                $obj = new $class();
                if (method_exists($obj, $method)) {
                    $obj->$method($client_id, $message);
                }
            }
        }
    }

    /**
     * 当用户断开连接时触发
     */
    public static function onClose($client_id)
    {
        Gateway::sendToAll(json_encode([
            'type' => 'close',
            'client_id' => $client_id,
        ]));
    }
}