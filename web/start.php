<?php

use app\helpers\ResponseHelper;
use app\helpers\Sign;
use Workerman\Worker;

/**
 * 引入index.php，初始化Yii框架
 */
require_once __DIR__ . "/index.php";

function process($class, $method)
{
    $replacement = array_map(function ($item) {
        return "-{$item}";
    }, range('a', 'z'));
    $method = str_replace(range('A', 'Z'), $replacement, $method);
    $data = Yii::$app->request->post();
    if (!Sign::simpleSignCheck($data)) {
        return ResponseHelper::instance()->json('签名错误!');
    }
    try {
        $controller = Yii::$app->createControllerByID($class);
        if ($controller) {
            $res = $controller->runAction($method);
        } else {
            $res = ResponseHelper::instance()->json('Class Not Found');
        }
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        $res = ResponseHelper::instance()->json($msg, $e->getTraceAsString());
    }
    return $res;
}

function httpProcess($connection)
{
    $uri = $_SERVER['REQUEST_URI'];
    $pos = strpos($uri, '?') ?: strlen($uri);
    $uri = trim(substr($uri, 0, $pos), '/');
    $info = explode('/', $uri);
    if (empty($info[0]) || empty($info[1])) {
        $connection->send('非法请求!');
        return;
    }
    list($class, $method) = $info;
    /** 接口检查 End */
    $res = process(strtolower($class), $method);
    $connection->send($res);
}

/**
 * 主要的处理函数.
 * @param $connection
 * @param $data
 */
function textProcess($connection, $data)
{
    $data = @json_decode($data, true);
    if (empty($data['controller']) || empty($data['action'])) {
        $connection->send('非法请求!');
        return;
    }
    $class = strtolower($data['controller']);
    $method = $data['action'];
    Yii::$app->request->setBodyParams($data['params']);
    $response = process($class, $method);
    $connection->send($response);
}

$workers = Yii::$app->params['workers'];

/** 创建一个TextWorker **/
$textWorker = new Worker("text://0.0.0.0:{$workers['text']['port']}");
$textWorker->name = $workers['text']['name'];
// 启动4个进程对外提供服务
$textWorker->count = $workers['text']['count'];
$textWorker->onMessage = function ($connection, $data) {
    textProcess($connection, $data);
};

/** 创建一个httpWorker */
$httpWorker = new Worker("http://0.0.0.0:{$workers['http']['port']}");
$httpWorker->name = $workers['http']['name'];
// 启动4个进程对外提供服务
$httpWorker->count = $workers['http']['count'];
$httpWorker->onMessage = function ($connection) {
    httpProcess($connection);
};

Worker::runAll();