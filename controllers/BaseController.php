<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\helpers\ResponseHelper;

class BaseController extends Controller
{

    public $enableCsrfValidation = false;

    public function init()
    {
        parent::init();
    }

    public function actionTest()
    {
        return ResponseHelper::instance()->success('sss', Yii::$app->request->post());
    }

    public function actionTestA()
    {
        return ResponseHelper::instance()->success('sss', Yii::$app->request->post());
    }

}
