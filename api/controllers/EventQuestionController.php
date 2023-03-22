<?php

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\event\EventQuestionCreateForm;
use api\forms\event\EventQuestionDetailForm;
use api\forms\event\EventQuestionListForm;
use Yii;

class EventQuestionController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'create',
                'list',
                'detail'
            ]
        ];

        return $behaviors;
    }


    public function actions()
    {
        return [
            'create' => [
                'class'          => FormAction::class,
                'formClass'      => EventQuestionCreateForm::class,
                'messageSuccess' => Yii::t('app', 'Create Event Question success'),
                'messageFailed'  => Yii::t('app', 'Create Event Question failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'list'   => [
                'class'          => FormAction::class,
                'formClass'      => EventQuestionListForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Event Question list success'),
                'messageFailed'  => \Yii::t('app', 'Load Event Question list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'detail' => [
                'class'          => FormAction::class,
                'formClass'      => EventQuestionDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Event Question Detail success'),
                'messageFailed'  => \Yii::t('app', 'Load Event Question Detail failed'),
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
            'create' => ['post'],
            'list'   => ['post'],
            'detail' => ['post'],
        ];
    }
}