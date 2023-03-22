<?php

namespace api\filters;

use api\components\HttpException;
use yii\base\ActionFilter;
use yii\base\InvalidConfigException;
use yii\helpers\ArrayHelper;

class ClientFilter extends ActionFilter
{
    public $appKey;
    public $clientKeyHeaderKey;
    public $clientSecretHeaderKey;

    /**
     * @since 2018-02-13 14:07:03
     *
     * @param \yii\base\Action $action
     *
     * @return bool
     * @throws HttpException
     * @throws InvalidConfigException
     */
    public function beforeAction($action)
    {
        if(empty($this->appKey)){
            throw new InvalidConfigException(\Yii::t('app', 'Please setup $appKey when using this class as filter .'));
        }

        if (empty($this->clientKeyHeaderKey) || empty($this->clientSecretHeaderKey)) {
            throw new InvalidConfigException(\Yii::t('app', 'Please setup $appKeyHeaderKey and $appSecretHeaderKey when using this class as filter.'));
        }

        $clientKey    = \Yii::$app->request->headers->get($this->clientKeyHeaderKey);
        $clientSecret = \Yii::$app->request->headers->get($this->clientSecretHeaderKey);

        if (empty($clientKey) || empty($clientSecret)) {
            throw new HttpException(400, \Yii::t('app', 'Please provide the security of client key and client secret'));
        }

        $validClient = \Yii::$app->params['clientConfig'][$this->appKey];

        if($validClient[$this->clientKeyHeaderKey] != $clientKey || $validClient[$this->clientSecretHeaderKey] != $clientSecret){
            throw new HttpException(400, \Yii::t('app', 'Either one or both of your app key and app secret is invalid.'));
        }

        if($allowedIPs =ArrayHelper::getValue($validClient, 'allowedIPs')){
            if(!ArrayHelper::isIn(\Yii::$app->request->getUserIP(),$allowedIPs)) {
                throw new HttpException(400, \Yii::t('app', 'Your app can only be accessed from specific IP.'));
            }
        }

        return true;
    }
}
