<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\Response;

class BaseController extends Controller
{
    const CODE_SUCCESS = 0;

    const CODE_FAILED = 1;

    public function init()
    {
        parent::init();
    }

    public function displayJson($msg, $data = null, $code = self::CODE_FAILED)
    {
        $data = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ];
        return json_encode($data);
    }

    public function actionTest()
    {
        return $this->displayJson('success', ['test'], self::CODE_SUCCESS);
    }

}
