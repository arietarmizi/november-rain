<?php


namespace api\controllers;


use api\components\Controller;
use api\components\FormAction;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\point\TopUserForm;

class TopUserController extends Controller
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
            'list' => [
                'class'          => FormAction::class,
                'formClass'      => TopUserForm::class,
                'messageSuccess' => \Yii::t('app', 'Load Top User list success'),
                'messageFailed'  => \Yii::t('app', 'Load Top User list failed'),
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