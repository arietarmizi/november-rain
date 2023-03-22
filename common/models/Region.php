<?php

namespace common\models;

use common\base\ActiveRecord;

/**
 * Class Region
 *
 * @package common\models
 * @property string $id
 * @property string $name
 * @property string $description
 * @property string $picEmail
 * @property string $picPhoneNumber
 * @property string $picAddress
 * @property string $picName
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class Region extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    /**
     * @return \yii\db\Connection
     */
    public static function getDb()
    {
        return \Yii::$app->db;
    }

    /**
     * @inheritdoc
     */

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%region}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'             => \Yii::t('app', 'Code'),
            'name'           => \Yii::t('app', 'Name'),
            'description'    => \Yii::t('app', 'Description'),
            'picEmail'       => \Yii::t('app', 'PIC Email'),
            'picPhoneNumber' => \Yii::t('app', ' PIC Phone Number'),
            'picAddress'     => \Yii::t('app', 'PIC Address'),
            'status'         => \Yii::t('app', 'Status'),
            'createdAt'      => \Yii::t('app', 'Created At'),
            'updatedAt'      => \Yii::t('app', 'Updated At'),
        ];
    }

}