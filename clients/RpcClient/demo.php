<?php

require_once __DIR__ . '/TextClient.php';
require_once __DIR__ . '/HttpClient.php';
require_once __DIR__ . '/RemoteGet.php';

$textConfig = array(
    'source' => 'go2',
    'service' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'mapType' => 'rand',
        'app' => 'test',
        'secret' => 'test'
    ),
    'service1' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'mapType' => 'rand',
        'app' => 'test',
        'secret' => 'test'
    ),
);

$httpConfig = array(
    'source' => 'go2',
    'service' => array(
        'host' => '127.0.0.1:8801',
        'mapType' => 'rand',
        'app' => 'test',
        'secret' => 'test'
    ),
);

\RpcClient\TextClient::config($textConfig);
\RpcClient\HttpClient::config($httpConfig);

$res = \RpcClient\TextClient::inst('service')->setClass('user')->userInfo([
    'userId' => 210205,
//    'productId' => 1639817,
]);
echo var_export($res, true);