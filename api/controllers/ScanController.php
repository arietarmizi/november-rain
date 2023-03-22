<?php

namespace api\controllers;

use api\components\Controller;
use api\components\FormAction;
use api\components\Response;
use api\config\ApiCode;
use api\filters\ContentTypeFilter;
use api\forms\scan\PresenceForm;
use common\models\User;

class ScanController extends Controller
{
    public function behaviors()
    {
        $behaviors                        = parent::behaviors();
        $behaviors['content-type-filter'] = [
            'class'       => ContentTypeFilter::class,
            'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
            'only'        => [
                'presence',
            ]
        ];

        return $behaviors;
    }


    public function actions()
    {
        return [
            'presence' => [
                'class'          => FormAction::class,
                'formClass'      => PresenceForm::class,
                'messageSuccess' => \Yii::t('app', 'success'),
                'messageFailed'  => \Yii::t('app', 'failed'),
                'apiCodeSuccess' => ApiCode::USER_REGISTER_SUCCESS,
                'apiCodeFailed'  => ApiCode::USER_REGISTER_FAILED,
                'statusSuccess'  => 200,
                'statusFailed'   => 400,
            ],
        ];
    }

    public function actionCheckPoint()
    {
        /** @var User $user */
        $user = \Yii::$app->user->identity;

        $response          = new Response();
        $response->status  = 200;
        $response->name    = 'Check Point';
        $response->code    = ApiCode::CHECK_POINT_SUCCESS;
        $response->message = \Yii::t('app', 'Check point success');
        $response->data    = ['point' => $user->currentPoint];

        return $response;
    }

    protected function verbs()
    {
        return [
            'presence'             => ['post'],
            'point-history'        => ['get'],
            'check-point'          => ['get'],
            'point-history-filter' => ['post'],
        ];
    }
}