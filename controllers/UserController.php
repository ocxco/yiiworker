<?php

namespace app\controllers;

use Yii;
use app\helpers\ResponseHelper;
use yii\web\User;

class UserController extends BaseController
{

    public function actionUserInfo()
    {
        $userId = Yii::$app->request->post('userId');
        if (empty($userId)) {
            return ResponseHelper::instance()->json('请输入用户ID');
        }
        $user = \app\models\User::findOne(['id' => $userId]);
        return ResponseHelper::instance()->success('success', $user->toArray(['id', 'username', 'mobile']));
    }

}
