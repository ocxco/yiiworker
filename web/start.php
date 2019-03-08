<?php

use app\helpers\ResponseHelper;
use app\helpers\SignHelper;
use Workerman\Worker;

/**
 * 引入index.php，初始化Yii框架
 */
require_once __DIR__ . "/index.php";

function process($class, $method)
{
    $method = preg_replace('/([A-Z])/', '-$1', $method);
    $method = strtolower($method);

    $data = Yii::$app->request->post();
    if (!SignHelper::simpleSignCheck($data)) {
        return ResponseHelper::instance()->json('签名错误!');
    }

    // 取出远程地址放入全局变量中，方便取用.
    Yii::$app->params['remoteIP'] =  $data['remoteIP'];

    try {
        $controller = Yii::$app->createControllerByID($class);
        if ($controller) {
            $res = $controller->runAction($method);
        } else {
            $res = ResponseHelper::instance()->failed('Class Not Found');
        }
    } catch (\Exception $e) {
        $msg = $e->getMessage();
        $res = ResponseHelper::instance()->failed($msg, $e->getTraceAsString());
    }
    // 所有的action都返回ResponseHelper对象.
    // 在此处统一转换为json格式字符串返回给客户端.
    return $res->toJson();
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
    $res = process(urldecode(strtolower($class)), $method);
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
    // 把接收到的参数转换为post参数，方便接口处理.
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
