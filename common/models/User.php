<?php

namespace common\models;

use nadzif\file\models\File;
use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\IdentityInterface;

/**
 * Class User
 *
 * @package common\models
 * @property string        $id
 * @property string        $fileId
 * @property string        $companyId
 * @property string        identityCardNumber
 * @property string        $type
 * @property string        $name
 * @property string        $phoneNumber
 * @property string        $email
 * @property string        $birthDate
 * @property string        $birthPlace
 * @property string        $position
 * @property string        $passwordHash
 * @property string        $authKey
 * @property string        $passwordResetToken
 * @property string        $verified
 * @property string        $accessToken
 * @property string        $regionId
 * @property string        $segmen
 * @property string        $grade
 * @property string        $instagram
 * @property string        $address
 * @property string        $villageId
 * @property string        $hobby
 * @property string        $blood
 * @property string        $status
 * @property string        $memberId
 * @property string        $covidStatus
 * @property string        $createdAt
 * @property string        $updatedAt
 *
 * @property Company       $company
 * @property UserContact[] $contact
 * @property Company       $village
 * @property File          $file
 * @property Region        $region
 *
 * @property Device[]      $devices
 * @property Device[]      $activeDevices
 */
class User extends Model implements IdentityInterface
{
    public $id;

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';


    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function getId(){
        return $this->id;
    }

    public static function findIdentity($id)
    {
        return new User(['id'=>'123123']);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {

        if(ArrayHelper::isIn($token, ArrayHelper::getValue(Yii::$app->params, ['clientConfig', 'lpgTracking', ''])) ){

        }

        return new User(['id' => '213231331213']);
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
