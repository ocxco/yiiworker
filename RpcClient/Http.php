<?php
/**
 * worker调用客户端.
 * User: CxC
 * Date: 2017/7/3
 * Time: 16:19
 */

namespace RpcClient;

class Http
{

    private static $_instance   = null;
    private        $remote      = '';
    private        $remoteClass = '';
    private static $config      = array();

    public static function error($msg, $code = 1)
    {
        echo "code:$code, message:$msg\n";
        exit;
    }

    public static function config($config = array())
    {
        self::$config = $config;
    }

    public static function inst($remote)
    {
        if (!isset(self::$config[$remote])) {
            self::error("未找到{$remote}的相关配置！");
        }
        if (!isset(self::$_instance[$remote]) || !self::$_instance[$remote] instanceof self) {
            self::$_instance[$remote] = new self();
        }
        self::$_instance[$remote]->remote = $remote;
        return self::$_instance[$remote];
    }

    public function setClass($class)
    {
        if (empty($class) || !is_string($class)) {
            self::error('class名称必须为字符串！');
        }
        $this->remoteClass = $class;
        return $this;
    }

    public function __call($name, $arguments)
    {
        $config = self::$config[$this->remote];
        $server = RemoteGet::rand($config);
        $host = $server['host'];
        $port = $server['port'];
        if (strpos($host, 'http') === 0) {
            $url = "{$host}:{$port}/{$this->remoteClass}/$name?";
        } else {
            $url = "http://{$host}:{$port}/{$this->remoteClass}/$name?";
        }
        $expireAt = time() + 60;
        $params = self::sign($config['app'], $config['secret'], $expireAt, $arguments[0] ?? null);
        $queryString = http_build_query($params);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
        $result = curl_exec($ch);
        if (!empty(self::$config['debug'])) {
            $data = [
                'url' => $url,
                'queryString' => $queryString,
                'resp' => $result,
            ];
            echo var_export($data, true);
        }
        return @json_decode($result, true);
    }

    private static function sign($app, $secret, $timestamp, $params)
    {
        $sign = self::getSignString($secret, $timestamp);
        $params['app'] = $app;
        $params['sign'] = $sign;
        $params['timestamp'] = $timestamp;
        return $params;
    }

    private static function getSignString($secret, $timestamp)
    {
        return md5(md5($secret) . $timestamp);
    }
}