<?php

// add unit testing specific bootstrap code here

require_once __DIR__ . "/../../RpcClient/TextClient.php";

$textConfig = array(
    'source' => 'go2',
    'passport' => array(
        'host' => [
            '127.0.0.1:8802',
        ],
        'mapType' => 'rand',
        'app'  => 'test',
        'secret' => 'test'
    ),
);

\RpcClient\TextClient::config($textConfig);