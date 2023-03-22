<?php

namespace api\controllers;

use api\components\Controller;
use api\components\Response;

class UserController extends Controller
{

    public function actionPoint()
    {
        return new Response([
            'name'    => \Yii::t('app', 'Get Point Success'),
            'message' => \Yii::t('app', 'Point loaded successfully'),
            'code'    => 101,
            'status'  => 200,
            'data'    => ['point' => 1000]
        ]);
    }
}