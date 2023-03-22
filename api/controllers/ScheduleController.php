<?php


namespace api\controllers;


use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\schedule\ScheduleDayListForm;
use api\forms\schedule\ScheduleDetailForm;
use api\forms\schedule\ScheduleListForm;

class ScheduleController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'list',
                'day-list',
                'detail'
            ]
        ];
        return $behaviors;
    }

    public function actions()
    {
        return [
            'list'     => [
                'class'          => FormAction::class,
                'formClass'      => ScheduleListForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Schedule list success'),
                'messageFailed'  => \Yii::t('app', 'Load Schedule list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'day-list' => [
                'class'          => FormAction::class,
                'formClass'      => ScheduleDayListForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Schedule Day list success'),
                'messageFailed'  => \Yii::t('app', 'Load Schedule Day list failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
            'detail'   => [
                'class'          => FormAction::class,
                'formClass'      => ScheduleDetailForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Schedule Detail success'),
                'messageFailed'  => \Yii::t('app', 'Load Schedule Detail failed'),
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
            'list'     => ['post'],
            'day-list' => ['post'],
            'detail'   => ['post'],
        ];
    }

}