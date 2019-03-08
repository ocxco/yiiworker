<?php

namespace RpcClient;

use Yii;

class Client
{
    protected $remote = '';
    protected $remoteClass = '';

    protected static $_instance;
    protected static $config;

    public static function error($msg, $code = 1)
    {
        echo "code:$code, message:$msg\n";
        exit;
    }

    public static function config($config = array())
    {
        static::$config = $config;
    }

    /**
     * @param $remote
     *
     * @return Client
     */
    public static function inst($remote)
    {
        if (empty(static::$config[$remote])) {
            self::error("未找到{$remote}的相关配置！");
        }
        if (!isset(static::$_instance[$remote]) || !static::$_instance[$remote] instanceof static) {
            static::$_instance[$remote] = new static();
        }
        static::$_instance[$remote]->remote = $remote;
        return static::$_instance[$remote];
    }

    public function setClass($class)
    {
        if (empty($class) || !is_string($class)) {
            self::error('class名称必须为字符串！');
        }
        $this->remoteClass = $class;
        return $this;
    }

    protected static function sign($app, $secret, $timestamp, $params)
    {
        $sign = self::getSignString($secret, $timestamp);
        $params['app'] = $app;
        $params['sign'] = $sign;
        $params['timestamp'] = $timestamp;
        return $params;
    }

    protected static function getSignString($secret, $timestamp)
    {
        return md5(md5($secret) . $timestamp);
    }

    public static function getConfig()
    {
        return static::$config;
    }

    public static function getRemoteIp()
    {
        if (class_exists('yii')) {
            // 在Yii系统里面调用.
            $ip = yii::$app->request->getRemoteIP();
        } elseif (defined('CI_VERSION')) {
            // CI系统中调用
            $ci = &get_instance();
            $ip = $ci->input->ip_address();
        } elseif (isset($_SERVER['REMOTE_ADDR'])) {
            // 其他框架直接读取SERVER数据.
            $ip = $_SERVER['REMOTE_ADDR'];
        } else {
            // 如果不是运行在fpm环境
            // 获取本机IP.
            $ip = self::getLocalIP();
        }
        return $ip;
    }

    /**
     * 获取本机IP
     * @return string
     */
    private static function getLocalIP()
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        if (PHP_OS == 'WINNT') {
            // 获取操作系统为win2000/xp、win7的本机IP真实地址
            exec("ipconfig", $out, $stats);
            if (!empty($out)) {
                foreach ($out AS $row) {
                    if (strstr($row, "IP") && strstr($row, ":") && !strstr($row, "IPv6")) {
                        $tmpIp = explode(":", $row);
                        if (preg_match($preg, trim($tmpIp[1]))) {
                            return trim($tmpIp[1]);
                        }
                    }
                }
            }
        } elseif (PHP_OS == 'Darwin') {
            // 获取操作系统为linux类型的本机IP真实地址
            exec("ifconfig en0", $out, $stats);
            if (!empty($out[4])) {
                $info = explode(' ', trim($out[4]));
                if (!empty($info[1])) {
                    return $info[1];
                }
            }
        } else {
            // 获取操作系统为linux类型的本机IP真实地址
            exec("ifconfig", $out, $stats);
            if (!empty($out)) {
                if (isset($out[1]) && strstr($out[1], 'addr:')) {
                    $tmpArray = explode(":", $out[1]);
                    $tmpIp = explode(" ", $tmpArray[1]);
                    if (preg_match($preg, trim($tmpIp[0]))) {
                        return trim($tmpIp[0]);
                    }
                }
            }
        }
        return '127.0.0.1';
    }

    /**
     * 每次请求生成唯一ID，方便追踪日志.
     */
    public static function buildRequestId()
    {
        return md5(crc32(microtime()));
    }

}
