<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Ramsey\Uuid\Uuid;
use yii\behaviors\AttributeBehavior;

/**
 * Class Device
 *
 * @package common\models
 *
 * @property string  $id
 * @property string  $userId
 * @property string  $accessToken
 * @property string  $firebaseToken
 * @property integer $osType
 * @property string  $osVersion
 * @property string  $identifier
 * @property string  $playerId
 * @property string  $model
 * @property string  $appVersion
 * @property double  $latitude
 * @property double  $longitude
 * @property string  $lastIp
 * @property string  $timezone
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 * @property string  $verificationCode
 *
 * @property User    $userWithCompany
 * @property User    $user
 */
class Device extends ActiveRecord
{

    const STATUS_ACTIVE     = 'active';
    const STATUS_INACTIVE   = 'inactive';
    const STATUS_DELETED    = 'deleted';
    const STATUS_UNVERIFIED = 'unverified';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const VERIFICATION_CODE_EXP_DURATION = 60 * 10; // 10 minutes

    /**
     * @return string
     */
    public static function tableName()
    {
        return '{{%device}}';
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE     => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE   => \Yii::t('app', 'Inactive'),
            self::STATUS_DELETED    => \Yii::t('app', 'Deleted'),
            self::STATUS_UNVERIFIED => \Yii::t('app', 'Unverified'),
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        $behaviors['accessToken'] = [
            'class'      => AttributeBehavior::class,
            'value'      => function ($event) {
                return \str_replace('-', '', Uuid::uuid4()->toString()) . '.' . \str_replace('-', '',
                        Uuid::uuid4()->toString());
            },
            'attributes' => [
                ActiveRecord::EVENT_BEFORE_INSERT => 'accessToken',
            ],
        ];

        return $behaviors;
    }

    public function attributeLabels()
    {
        return [
            'id'            => \Yii::t('app', 'Id user'),
            'userId'        => \Yii::t('app', 'User ID'),
            'accessToken'   => \Yii::t('app', 'Access Token'),
            'firebaseToken' => \Yii::t('app', 'Firebase Token'),
            'osType'        => \Yii::t('app', 'Os Type'),
            'osVersion'     => \Yii::t('app', 'Os Version'),
            'identifier'    => \Yii::t('app', 'identifier'),
            'playerId'      => \Yii::t('app', 'Player Id'),
            'model'         => \Yii::t('app', 'Model'),
            'appVersion'    => \Yii::t('app', 'App Version'),
            'latitude'      => \Yii::t('app', 'Latitude'),
            'longitude'     => \Yii::t('app', 'Longitude'),
            'lastIp'        => \Yii::t('app', 'Last Ip'),
            'timezone'      => \Yii::t('app', 'Timezone'),
            'status'        => \Yii::t('app', 'status'),
            'createdAt'     => \Yii::t('app', 'Created At'),
            'updatedAt'     => \Yii::t('app', 'Updated At')
        ];
    }

    public function init()
    {
        parent::init();

        $this->on(ActiveRecord::EVENT_BEFORE_INSERT, [$this, 'deactivateOtherDevice']);
    }

    public function rules()
    {
        return [
            [['userId', 'identifier'], 'required'],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();

        $scenarios[self::SCENARIO_CREATE] = [
            'userId',
            'osType',
            'osVersion',
            'identifier',
            'firebaseToken',
            'playerId',
            'model',
            'appVersion',
            'latitude',
            'longitude',
            'lastIp',
            'timezone',
        ];
        $scenarios[self::SCENARIO_UPDATE] = [
            'osType',
            'osVersion',
            'identifier',
            'playerId',
            'model',
            'appVersion',
            'latitude',
            'longitude',
            'lastIp',
            'timezone',
        ];

        return $scenarios;
    }

    /**
     * @return \yii\db\ActiveQuery
     */

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }


    public function deactivateOtherDevice()
    {
        $this->status = static::STATUS_ACTIVE;
        Device::updateAll(['status' => static::STATUS_INACTIVE], [
            'userId' => $this->userId,
            'status' => static::STATUS_ACTIVE,
        ]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserWithCompany()
    {
        return $this->hasOne(User::class, ['id' => 'userId'])->with('company');
    }
}