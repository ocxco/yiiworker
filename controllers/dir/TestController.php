<?php

namespace app\controllers\dir;

use Yii;
use app\helpers\ResponseHelper;
use app\controllers\BaseController;

class TestController extends BaseController
{

    public function actionTestInDir()
    {
        return ResponseHelper::instance()->success('success', 'success access to testController in go2 dir');
    }

}
