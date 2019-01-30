<?php
/**
 * Created by PhpStorm.
 * User: CxC
 * Date: 2017/4/15
 * Time: 22:32
 */

require_once __DIR__ . '/Text.php';
require_once __DIR__ . '/Http.php';
require_once __DIR__ . '/RemoteGet.php';

$textConfig = array(
    'push-service' => array(
//        'host' => '192.168.10.151',
        'host' => [
            '127.0.0.1:8802',
            '127.0.0.1:8803',
            '127.0.0.1:8804',
        ],
        'app'  => 'test',
        'secret' => 'test'
    ),
);
$httpConfig = array(
    'push-service' => array(
//        'host' => '192.168.10.151',
        'host' => '127.0.0.1:8801',
        'app'  => 'test',
        'secret' => 'test'
    ),
);

\RpcClient\Text::config($textConfig);
\RpcClient\Http::config($httpConfig);

//$r = \RpcClient\Http::inst('push-service')->setClass('base')->test();
//var_dump($r);
$content = 'TextRequest测试能收到吗';
$r = \RpcClient\Text::inst('push-service')->setClass('Base')->testA(['a' => 1, 'b' => 2]);
var_dump($r);
exit;