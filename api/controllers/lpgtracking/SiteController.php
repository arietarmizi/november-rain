<?php

namespace api\controllers\lpgtracking;

use api\controllers\lpgtracking\Controller;
use api\components\Response;

class SiteController extends Controller
{
    public function behaviors()
    {
        $behaviors                            = parent::behaviors();
        $behaviors['authenticator']['except'] = ['index', 'version', 'security'];
        $behaviors['clientFilter']['except']  = ['index', 'version'];
        return $behaviors;
    }

    public function actionIndex()
    {
        $response = new Response();

        $response->name    = \Yii::$app->name;
        $response->message = \Yii::t('app', 'API is running');
        $response->code    = 0;
        $response->status  = 200;
        $response->data    = \Yii::t('app', 'You are accessing this endpoint from ') . \Yii::$app->request->getUserIP();

        return $response;
    }

    public function actionVersion()
    {
        $response          = new Response();
        $response->name    = \Yii::t('app', 'Version Check');
        $response->message = \Yii::t('app', 'Check application versioning to continue');
        $response->code    = 0;
        $response->status  = 200;
        $response->data    = [
            'ForceUpdate'     => true,
            'AllowedVersions' => ["Android" => ['1.0.1', '1.0.2'], "iOS" => ["2.0.1"]]
        ];
        return $response;
    }

    public function actionSecurity()
    {
        $response          = new Response();
        $response->name    = \Yii::t('app', 'Security Passed');
        $response->message = \Yii::t('app', 'Your app passed client security check');
        $response->code    = 0;
        $response->status  = 200;
        return $response;
    }

    protected function verbs()
    {
        return [
            'index'    => ['get'],
            'version'  => ['get'],
            'security' => ['get'],
        ];
    }
}