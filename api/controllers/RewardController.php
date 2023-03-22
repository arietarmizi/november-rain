<?php

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\reward\RewardDetailForm;
use api\forms\reward\RewardForm;

class RewardController extends Controller
{

    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'list',
                'detail'
            ]
        ];

        return $behaviors;
    }

    public function actions()
    {
        return [
            'list'   => [
                'class'          => FormAction::class,
                'formClass'      => RewardForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Reward list success'),
                'messageFailed'  => \Yii::t('app', 'Load Reward list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'detail' => [
                'class'          => FormAction::class,
                'formClass'      => RewardDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Reward Detail success'),
                'messageFailed'  => \Yii::t('app', 'Load Reward Detail failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
        ];
    }

    protected function verbs()
    {
        return [
            'list'   => ['post'],
            'detail' => ['post'],
        ];
    }
}