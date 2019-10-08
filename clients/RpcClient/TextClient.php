<?php
/**
 * worker调用客户端.
 * User: CxC
 * Date: 2017/7/3
 * Time: 16:19
 */

namespace RpcClient;

require_once __DIR__ . '/Client.php';

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
        $arguments[0]['requestSource'] = static::$config['source'];
        $arguments[0]['requestId'] = self::buildRequestId();
        $params = self::sign($config['app'], $config['secret'], $expireAt, $arguments[0]);
        $data = array(
            'controller' => $this->remoteClass,
            'action'  => $name,
            'params'  => $params,
        );

        if (version_compare(PHP_VERSION, '5.4.0','<')) {
            $data = preg_replace_callback('#\\\u([0-9a-f]{4})#i', function($matches) {
                $code = intval(hexdec($matches[1]));
                $ord_1 = decbin(0xe0 | ($code >> 12));
                $ord_2 = decbin(0x80 | (($code >> 6) & 0x3f));
                $ord_3 = decbin(0x80 | ($code & 0x3f));
                return chr(bindec($ord_1)) . chr(bindec($ord_2)) . chr(bindec($ord_3));
            }, json_encode($data)) . "\n";
        } else {
            $data = json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
        }

        fwrite($connection, $data);
        $result = trim(fgets($connection));
        fclose($connection);
        if (!empty(self::$config['debug'])) {
            $data = array(
                'data' => $data,
                'resp' => $result,
            );
            echo var_export($data, true);
        }
        return @json_decode($result, true);
    }

}
