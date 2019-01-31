<?php

namespace app\controllers;

use Yii;
use app\helpers\ResponseHelper;

class TestController extends BaseController
{

    public function actionTest()
    {
        return ResponseHelper::instance()->success('sss', Yii::$app->request->post());
    }

}
