<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;

/**
 * Class SystemApp
 *
 * @package common\models
 *
 * @property string $id
 * @property string $name
 * @property string $appKey
 * @property string $appSecret
 * @property string $type
 * @property string $ip
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class SystemApp extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_BLOCKED  = 'blocked';

    const TYPE_DEVICE      = 'device';
    const TYPE_SERVICE     = 'service';
    const TYPE_THIRD_PARTY = 'thirdParty';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%system_app}}';
    }

    public function attributeLabels()
    {
        return [
            'id'        => \Yii::t('app', 'Id'),
            'name'      => \Yii::t('app', 'Name'),
            'appKey'    => \Yii::t('app', 'App Key'),
            'appSecret' => \Yii::t('app', 'App Secret'),
            'type'      => \Yii::t('app', 'Type'),
            'ip'        => \Yii::t('app', 'Ip'),
            'status'    => \Yii::t('app', 'Status'),
            'createdAt' => \Yii::t('app', 'Created At'),
            'updatedAt' => \Yii::t('app', 'Updated At'),
        ];
    }
}
