<?php

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\news\NewsDetailForm;
use api\forms\news\NewsForm;

class NewsController extends Controller
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
                'formClass'      => NewsForm::class,
                'messageSuccess' => \Yii::t('app', 'Load News list success'),
                'messageFailed'  => \Yii::t('app', 'Load News list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'detail' => [
                'class'          => FormAction::class,
                'formClass'      => NewsDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Load News Detail success'),
                'messageFailed'  => \Yii::t('app', 'Load News Detail failed'),
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