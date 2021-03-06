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

    private static $config;

    public static function get($config, $uri = '')
    {
        self::$config = $config;
        $mapType = $config['mapType'] ?: 'rand';
        switch ($mapType) {
            case 'hash':
                $remote = self::hash($uri);
                break;
            case 'loop':
                $remote = self::loop();
                break;
            case 'rand':
            default:
                $remote = self::rand();
        }
        list($host, $port) = explode(':', $remote);
        return compact('host', 'port');
    }

    private static function hosts()
    {
        if (is_array(self::$config['host'])) {
            $remote = self::$config['host'];
        } else {
            $remote = array(self::$config['host']);
        }
        return $remote;
    }

    private static function rand()
    {
        $hosts = self::hosts();
        $rand = rand() % count($hosts);
        $remote = $hosts[$rand];
        return $remote;
    }

    private static function hash($uri)
    {
        $hosts = self::hosts();
        $hash = crc32($uri) % count($hosts);
        return $hosts[$hash];
    }

    private static function loop()
    {
        // TODO 轮询需要借助redis或者其他缓存实现.
        // 暂时用rand
        return self::rand();
    }

}
