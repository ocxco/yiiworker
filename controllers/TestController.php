<?php

namespace app\controllers;

use Yii;
use app\helpers\ResponseHelper;

class TestController extends BaseController
{

    public function actionTest()
    {
        return ResponseHelper::instance()->success('success', Yii::$app->request->post());
    }

    public function actionTestList()
    {
        ResponseHelper::instance()->setPagination(1, 20, 10);
        return ResponseHelper::instance()->success('success', range(0, 9));
    }

    public function actionTestListWithUserInfo()
    {
        ResponseHelper::instance()->setPagination(1, 20, 10);
        ResponseHelper::instance()->setExtra('user', ['username' => 'test', 'state' => 1]);
        return ResponseHelper::instance()->success('success', range(0, 9));
    }

    public function actionTestParams()
    {
        $name = Yii::$app->request->post('name');
        return ResponseHelper::instance()->success('success', "Hello $name");
    }

}
