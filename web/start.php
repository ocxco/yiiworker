<?php

use app\helpers\ResponseHelper;
use app\helpers\SignHelper;
use Workerman\Worker;

/**
 * 引入index.php，初始化Yii框架
 */
require_once __DIR__ . "/index.php";
require_once __DIR__ . "/../clients/StatisticClient.php";

function process($class, $method)
{
    $method = preg_replace('/([A-Z])/', '-$1', $method);
    $method = strtolower($method);
    $success = false;
    $data = Yii::$app->request->post();
    Yii::$app->params['requestId'] = $data['requestId'];
    if (!SignHelper::simpleSignCheck($data)) {
        $res = ResponseHelper::instance()->failed('签名错误!');
    } else {
        // 取出远程地址放入全局变量中，方便取用.
        Yii::$app->params['remoteIP'] = $data['remoteIP'];
        Yii::$app->params['requestSource'] = $data['requestSource'];
        if (!empty(Yii::$app->params['statisticsReport'])) {
            // 状态上报，接入workerman-statistics
            $module = implode('@', explode('/', $class));
            StatisticClient::tick(Yii::$app->id, "{$module}::{$method}");
        }
        try {
            $controller = Yii::$app->createControllerByID($class);
            if ($controller) {
                $res = $controller->runAction($method, $data);
                $success = true;
            } else {
                $res = ResponseHelper::instance()->failed('Class Not Found');
            }
        } catch (\Exception $e) {
            $msg = $e->getMessage();
            ResponseHelper::instance()->setExtra(['errorCode' => $e->getCode()]);
            $res = ResponseHelper::instance()->failed($msg, $e->getTraceAsString());
        }

    }

    if (!empty(Yii::$app->params['statisticsReport'])) {
        // 状态上报
        $module = implode('@', explode('/', $class));
        StatisticClient::report(Yii::$app->id, "{$module}::{$method}", $success, $res->getCode(), mb_substr($res->toJson(), 0, 1000), Yii::$app->params['statisticsReport']);
    }
    // 所有的action都返回ResponseHelper对象.
    $resp = $res->toJson();
    $res->destroy();
    return $resp;
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
    Yii::$app->request->setBodyParams($data['params']);
    $response = process($class, $method);
    $connection->send($response);
}

/**
 * 监控每个子进程提供服务的次数
 * 达到一定次数，退出子进程，重新创建一个.
 */
function monitor()
{
    Yii::$app->params['processCount'] = Yii::$app->params['processCount'] ?? 1;
    Yii::$app->params['processCount']++;
    if (Yii::$app->params['processCount'] > 201) {
        $pid = posix_getpid();
        Yii::info("进程{$pid}达到指定服务次数，退出");
        Worker::stopAll();
    }
}

$workers = Yii::$app->params['workers'];

/** 创建一个TextWorker **/
$textWorker = new Worker("text://0.0.0.0:{$workers['text']['port']}");
$textWorker->name = $workers['text']['name'];
// 启动4个进程对外提供服务
$textWorker->count = $workers['text']['count'];
$textWorker->onMessage = function ($connection, $data) {
    textProcess($connection, $data);
    monitor();
};

/** 创建一个httpWorker */
$httpWorker = new Worker("http://0.0.0.0:{$workers['http']['port']}");
$httpWorker->name = $workers['http']['name'];
// 启动4个进程对外提供服务
$httpWorker->count = $workers['http']['count'];
$httpWorker->onMessage = function ($connection) {
    httpProcess($connection);
    monitor();
};

Worker::runAll();
