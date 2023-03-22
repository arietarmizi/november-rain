<?php


namespace common\models;

use common\models\User;
use nadzif\base\models\ActiveRecord;

/**
 * Class UserContact
 *
 * @package common\models
 * @property string $id
 * @property string $userId
 * @property string $relationship
 * @property string $phoneNumber
 * @property string $status
 * @property string $createdAt
 *
 *
 */
class UserContact extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%user_contact}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function attributeLabels()
    {
        return [
            'id'           => \Yii::t('app', 'Id'),
            'userId'       => \Yii::t('app', 'User Id'),
            'relationship' => \Yii::t('app', 'Relationship'),
            'phoneNumber'  => \Yii::t('app', 'Phone Number'),
            'status'       => \Yii::t('app', 'status'),
            'createdAt'    => \Yii::t('app', 'Created At'),
            'updatedAt'    => \Yii::t('app', 'Updated At')
        ];
    }
}