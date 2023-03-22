<?php

namespace common\helpers;

use api\models\User;
use yii\helpers\ArrayHelper;

class Project
{

    public static function isBearerTokenValid($appKey, $token )
    {
        $isValid = false;
        foreach (Project::getAppConfig($appKey, 'identities') as $identity) {
            if ($identity['AccessToken'] == $token) {
                $isValid=true;
                break;
            }
        }

        return $isValid;
    }

    public static function getAppConfig($appKey, $key){
        return ArrayHelper::getValue(\Yii::$app->params, ['clientConfig', $appKey, $key]);
    }

    public static function getIdentity($appKey, $identityData){
        foreach (Project::getAppConfig($appKey, 'identities') as $identity) {
            if ($identity['Identity'] == $identityData) {
                return $identity;
            }
        }

        return null;
    }

    public static function getRequestTime(){
        return \Yii::getLogger()->getElapsedTime();
    }

}