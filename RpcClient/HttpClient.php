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

class HttpClient extends Client
{
    protected static $_instance = array();
    protected static $config = array();

    public function __call($name, $arguments)
    {
        $config = static::$config[$this->remote];
        $remoteClass = urlencode($this->remoteClass);
        $server = RemoteGet::get($config, "$remoteClass/$name");
        $host = $server['host'];
        $port = $server['port'];
        if (strpos($host, 'http') === 0) {
            $url = "{$host}:{$port}/{$remoteClass}/$name?";
        } else {
            $url = "http://{$host}:{$port}/{$remoteClass}/$name?";
        }
        $expireAt = time() + 60;
        $arguments[0]['remoteIP'] = self::getRemoteIp();
        $arguments[0]['requestId'] = self::buildRequestId();
        $params = self::sign($config['app'], $config['secret'], $expireAt, $arguments[0] ?? null);
        $queryString = http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        $result = curl_exec($ch);
        if (!empty(static::$config['debug'])) {
            $data = [
                'url' => $url,
                'queryString' => $queryString,
                'resp' => $result,
            ];
            echo var_export($data, true);
        }
        return @json_decode($result, true);
    }

}
