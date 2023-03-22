<?php

namespace api\components;

use api\filters\ClientFilter;
use api\filters\ContentTypeFilter;
use common\filters\FirstRequestTimeFilter;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;

class Controller extends \yii\web\Controller
{
    public $enableCsrfValidation = false;
    public $appKey;
    public $hasJson              = [];

    /**
     * Override behaviors from rest controller
     *
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [
            'rateLimiter'            => [
                'class'        => RateLimiter::className(),
                'errorMessage' => \Yii::t('app', 'Too many request'),
            ],
            'contentNegotiator'      => [
                'class'   => ContentNegotiator::class,
                'formats' => [
                    'application/json' => \yii\web\Response::FORMAT_JSON
                ]
            ],
            'verbFilter'             => [
                'class'   => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'clientFilter'           => [
                'class'                 => ClientFilter::class,
                'appKey'                => $this->appKey,
                'clientKeyHeaderKey'    => 'X-Client-key',
                'clientSecretHeaderKey' => 'X-Client-secret'
            ],
            'authenticator'          => [
                'class'       => CompositeAuth::className(),
                'authMethods' => [HttpBearerAuth::className()]
            ],
            'firstRequestTimeFilter' => [
                'class' => FirstRequestTimeFilter::class
            ],

        ];

        if ($this->hasJson) {
            $behaviors['content-type-filter'] = [
                'class'       => ContentTypeFilter::class,
                'contentType' => ContentTypeFilter::TYPE_APPLICATION_JSON,
                'only'        => $this->hasJson
            ];
        }

        return $behaviors;
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

    /**
     * @param $action
     * @param $result
     *
     * @return mixed
     * @throws HttpException
     * @since 2018-02-02 09:39:14
     *
     */
    public function afterAction($action, $result)
    {
        return parent::afterAction($action, $result);
    }

    public function init()
    {
        parent::init();
    }
}
