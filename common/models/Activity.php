<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;

/**
 * Class Activity
 *
 * @package common\models
 * @property string  $id
 * @property string  $rundownId
 * @property string  $type
 * @property string  $name
 * @property string  $description
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property Rundown $rundown
 *
 */
class Activity extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const TYPE_PRESENCE  = 'presence';
    const TYPE_GAMES     = 'games';
    const TYPE_QUIZ      = 'quiz';
    const TYPE_BREAKFAST = 'breakfast';
    const TYPE_LUNCH     = 'lunch';
    const TYPE_DINNER    = 'dinner';
    const TYPE_BREAK     = 'break';
    const TYPE_OTHER     = 'other';

    public static function tableName()
    {
        return '{{%activity}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public static function types()
    {
        return [
            self::TYPE_PRESENCE  => Yii::t('app', 'Presence'),
            self::TYPE_GAMES     => Yii::t('app', 'Games'),
            self::TYPE_QUIZ      => Yii::t('app', 'Quiz'),
            self::TYPE_BREAKFAST => Yii::t('app', 'Breakfast'),
            self::TYPE_LUNCH     => Yii::t('app', 'Lunch'),
            self::TYPE_DINNER    => Yii::t('app', 'Dinner'),
            self::TYPE_BREAK     => Yii::t('app', 'Break'),
            self::TYPE_OTHER     => Yii::t('app', 'Other'),
        ];
    }

    public function attributeLabels()
    {
        return [];
    }

    public function getRundown()
    {
        return $this->hasOne(Rundown::class, ['id' => 'rundownId'])
            ->joinWith(['event']);
    }

}
