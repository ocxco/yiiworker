<?php

require_once __DIR__ . '/TextClient.php';
require_once __DIR__ . '/HttpClient.php';
require_once __DIR__ . '/RemoteGet.php';

$textConfig = array(
    'service1' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'mapType' => 'rand',
        'app'  => 'test',
        'secret' => 'test'
    ),
    'service2' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'mapType' => 'rand',
        'app'  => 'test',
        'secret' => 'test'
    ),
);

$httpConfig = array(
    'service' => array(
        'host' => '127.0.0.1:8801',
        'mapType' => 'rand',
        'app'  => 'test',
        'secret' => 'test'
    ),
);

\RpcClient\TextClient::config($textConfig);
\RpcClient\HttpClient::config($httpConfig);

// Test
$r = \RpcClient\TextClient::inst('service1')->setClass('test')->test();
var_dump($r);
$r = \RpcClient\TextClient::inst('service1')->setClass('user')->userInfo(['userId' => 216199]);
var_dump($r);
exit;
// Test Controller in subdir
$r = \RpcClient\TextClient::inst('service1')->setClass('test')->testParams(['name' => 'Chen']);
var_dump($r);
// Another rpc service
$r = \RpcClient\TextClient::inst('service2')->setClass('dir/test')->testInDir();
var_dump($r);
// Test Http Client
$r = \RpcClient\HttpClient::inst('service')->setClass('test')->testList();
var_dump($r);
$r = \RpcClient\HttpClient::inst('service')->setClass('test')->testListWithUserInfo();
var_dump($r);
