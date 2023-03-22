<?php


namespace api\components;

use api\filters\ClientFilter;
use common\filters\FirstRequestTimeFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\VerbFilter;
use yii\web\Controller;

class WebviewController extends Controller
{
    public $enableCsrfValidation = false;

    /**
     * Override behaviors from rest controller
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            'rateLimiter'            => [
                'class'        => \yii\filters\RateLimiter::className(),
                'errorMessage' => \Yii::t('app', 'Too many request'),
            ],
            'contentNegotiator'      => [
                'class'   => ContentNegotiator::class,
                'formats' => [
                    'text/html' => \yii\web\Response::FORMAT_HTML
                ]
            ],
            'verbFilter'             => [
                'class'   => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'systemAppFilter'        => [
                'class'              => ClientFilter::class,
                'appKeyHeaderKey'    => 'X-App-key',
                'appSecretHeaderKey' => 'X-App-secret'
            ],
            'authenticator'          => [
                'class'       => CompositeAuth::className(),
                'authMethods' => [HttpBearerAuth::className()]
            ],
            'firstRequestTimeFilter' => [
                'class' => FirstRequestTimeFilter::class
            ],

        ];
    }

    /**
     * Declares the allowed HTTP verbs.
     * Please refer to [[VerbFilter::actions]] on how to declare the allowed verbs.
     *
     * @return array the allowed HTTP verbs.
     */
    protected function verbs()
    {
        return [];
    }

    public function init()
    {
        parent::init();
        \Yii::$app->language = 'id';
    }
}