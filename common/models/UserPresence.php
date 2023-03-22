<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;

/**
 * Class UserPresence
 *
 * @package common\models
 * @property string  $id
 * @property string  $rundownId
 * @property string  $userId
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property User    $user
 * @property Rundown $rundown
 *
 */
class UserPresence extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%user_presence}}';
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
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function getRundown()
    {
        return $this->hasOne(Rundown::class, ['id' => 'rundownId']);
    }

}
