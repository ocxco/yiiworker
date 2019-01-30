<?php
/**
 * worker调用客户端.
 * User: CxC
 * Date: 2017/7/3
 * Time: 16:19
 */

namespace RpcClient;

class RemoteGet
{
    public static function rand($config)
    {
        if (is_array($config['host'])) {
            $rand = rand() % count($config['host']);
            $remote = $config['host'][$rand];
        } else {
            $remote = $config['host'];
        }
        list($host, $port) = explode(':', $remote);
        return compact('host', 'port');
    }

}