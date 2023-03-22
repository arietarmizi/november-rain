<?php

namespace api\models;

use common\helpers\Project;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

class User extends Model implements IdentityInterface
{

    public $Id;
    public $Identity;
    public $AccessToken;
    public $FirebaseToken;
    public $Role;
    public $AllowedPlatform;

    public function getId()
    {
        return $this->Id;
    }

    public static function findIdentity($id)
    {
        foreach (Project::getAppConfig('lpgTracking', 'identities') as $identity) {
            if ($identity['Id'] == $id) {
                return new User($identity);
            }
        }

        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        foreach (Project::getAppConfig('lpgTracking', 'identities') as $identity) {
            if ($identity['AccessToken'] == $token) {
                return new User($identity);
            }
        }

        return null;
    }

    public function getAuthKey()
    {
        return false;
    }

    public function validateAuthKey($authKey)
    {
        return false;
    }
}