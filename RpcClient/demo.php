<?php

require_once __DIR__ . '/Text.php';
require_once __DIR__ . '/Http.php';
require_once __DIR__ . '/RemoteGet.php';

$textConfig = array(
    'push-service' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'app'  => 'test',
        'secret' => 'test'
    ),
);

$httpConfig = array(
    'push-service' => array(
        'host' => '127.0.0.1:8801',
        'app'  => 'test',
        'secret' => 'test'
    ),
);

\RpcClient\Text::config($textConfig);
\RpcClient\Http::config($httpConfig);

$r = \RpcClient\Text::inst('push-service')->setClass('aaa')->aaa(['a' => 1, 'b' => 2]);
var_dump($r);
$r = \RpcClient\Http::inst('push-service')->setClass('user')->userInfo(['userId' => 210205]);
var_dump($r);
