<?php
/**
 * worker调用客户端.
 * User: CxC
 * Date: 2017/7/3
 * Time: 16:19
 */

namespace RpcClient;

require_once __DIR__ . '/Client.php';
require_once __DIR__ . '/RemoteGet.php';

class TextClient extends Client
{
    protected static $_instance = array();
    protected static $config = array();

    public function __call($name, $arguments)
    {
        $config = static::$config[$this->remote];
        $server = RemoteGet::get($config, "{$this->remoteClass}/$name");
        $host = $server['host'];
        $port = $server['port'];
        $connection = stream_socket_client("{$host}:{$port}");
        $expireAt = time() + 60; // 防止服务器时间相差过大, 设置60秒请求有效期,可以防止网络重放攻击.
        $arguments[0]['remoteIP'] = self::getRemoteIp();
        $arguments[0]['requestId'] = self::buildRequestId();
        $params = self::sign($config['app'], $config['secret'], $expireAt, $arguments[0] ?? null);
        $data = array(
            'controller' => $this->remoteClass,
            'action'  => $name,
            'params'  => $params,
        );
        $data = json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        fwrite($connection, $data);
        $result = trim(fgets($connection));
        fclose($connection);
        if (!empty(self::$config['debug'])) {
            $data = [
                'data' => $data,
                'resp' => $result,
            ];
            echo var_export($data, true);
        }
        return @json_decode($result, true);
    }

}
