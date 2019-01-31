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

}
