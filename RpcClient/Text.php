<?php
/**
 * worker调用客户端.
 * User: CxC
 * Date: 2017/7/3
 * Time: 16:19
 */

namespace RpcClient;

class Text
{

    private static $_instance = array();
    private $remote = '';
    private $remoteClass = '';
    private static $config = array();

    public static function error($msg, $code = 1)
    {
        echo "code:$code, message:$msg\n";
        exit;
    }

    public static function config($config = array()) {
        self::$config = $config;
    }

    public static function inst($remote)
    {
        if (empty(self::$config[$remote])) {
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
        $connection = stream_socket_client("{$host}:{$port}");
        $expireAt = time() + 60; // 防止服务器时间相差过大, 设置60秒请求有效期,可以防止网络重放攻击.
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