<?php

namespace common\components;

use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class Controller extends \yii\web\Controller
{
    public $allowedRoles = ['@'];

    public function behaviors()
    {
        $behaviors = [];

        if (!isset($this->behaviors['access'])) {
            $behaviors['access'] = [
                'class' => AccessControl::className(),
                'rules' => [
                    ['allow' => true, 'roles' => $this->allowedRoles]
                ]
            ];
        }

        $behaviors['verbFilter'] = [
            'class'   => VerbFilter::className(),
            'actions' => $this->verbs(),
        ];

        return $behaviors;

    }

    public function verbs()
    {
        return [];
    }

    public function init()
    {
        \Yii::$app->language = \Yii::$app->session->get('language', 'en');
        parent::init();

    }

}