<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;

/**
 * Class Participant
 *
 * @package common\models
 * @property string $id
 * @property string $eventId
 * @property string $userId
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property User   $user
 */
class Participant extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%participant}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId'])->joinWith(['company']);
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'eventId'])->joinWith(['file']);
    }
}
