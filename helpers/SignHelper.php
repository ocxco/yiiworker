<?php

namespace app\helpers;

use Yii;

class SignHelper
{
    /**
     * 简单的签名方式.
     *
     * @param $secret
     * @param $timestamp
     * @return string
     */
    public static function simpleSign($secret, $timestamp)
    {
        return md5(md5($secret) . $timestamp);
    }

    public static function simpleSignCheck($params)
    {
        $config = Yii::$app->params['auth'];
        if (empty($params['app']) || empty($params['sign']) || empty($params['timestamp'])) {
            return false;
        }
        $app = $params['app'];
        $sign = $params['sign'];
        $timestamp = $params['timestamp'];
        if ($timestamp < time()) {
            // 超过请求有效期.
            return false;
        }
        if (!isset($config[$app])) {
            return false;
        }
        $secret = $config[$app];
        $nowSign = strtolower(self::simpleSign($secret, $timestamp));
        return $nowSign == strtolower($sign);
    }

    /**
     * 生成sign字符串.
     *
     * @param $app
     * @param $secret
     * @param $timestamp
     * @param $params
     *
     * @return string
     */
    public static function getSignString($app, $secret, $timestamp, $params)
    {
        $signString = '';
        sort($params);
        foreach ($params as $item) {
            if (is_array($item)) {
                $signString .= json_encode($item);
            } else {
                $signString .= $item;
            }
        }
        $sign = md5($app . $secret . $timestamp . $signString);
        return $sign;
    }

    /**
     * 鉴权.
     * @param mixed $params 其余参数.
     *
     * @return bool
     */
    public static function checkSign($params)
    {
        $config = Yii::$app->params['auth'];
        if (empty($params['app']) || empty($params['sign']) || empty($params['timestamp'])) {
            return false;
        }
        $app = $params['app'];
        $sign = $params['sign'];
        $timestamp = $params['timestamp'];
        if ($timestamp < time()) {
            // 超过请求有效期.
            return false;
        }
        unset($params['app'], $params['sign'], $params['timestamp']);
        if (!isset($config[$app])) {
            return false;
        }
        $secret = $config[$app];
        $signString = self::getSignString($app, $secret, $timestamp, $params);
        return $signString == $sign;
    }

    /**
     * 生成签名.
     * @param $app
     * @param $timestamp
     * @param $params
     *
     * @return mixed
     */
    public static function sign($app, $timestamp, $params)
    {
        $config = Yii::$app->params['auth'];
        if (!isset($config[$app])) {
            return false;
        }
        $secret = $config[$app];
//        $sign = self::getSignString($app, $secret, $timestamp, $params);
        $sign = self::simpleSign($secret, $timestamp);
        $params['app'] = $app;
        $params['sign'] = $sign;
        $params['timestamp'] = $timestamp;
        return $params;
    }

}