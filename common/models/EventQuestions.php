<?php


namespace common\models;

use common\models\Gallery;
use common\models\User;
use nadzif\base\models\ActiveRecord;

/**
 * Class EventQuestions
 * @package common\models
 *
 * @property string $id
 * @property string $eventId
 * @property string $userId
 * @property string $questions
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class EventQuestions extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DELETED  = 'deleted';

    public static function tableName()
    {
        return '{{%event_question}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_DELETED  => \Yii::t('app', 'Deleted'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
            self::STATUS_ARCHIVED => \Yii::t('app', 'Archive'),
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => \Yii::t('app', 'ID'),
            'eventId'   => \Yii::t('app', 'Event'),
            'userId'    => \Yii::t('app', 'User'),
            'questions' => \Yii::t('app', 'Question'),
            'status'    => \Yii::t('app', 'Status'),
            'createdAt' => \Yii::t('app', 'Created At'),
            'updatedAt' => \Yii::t('app', 'Updated At'),
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'eventId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }
    public static function descriptions($string)
    {
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return $msg;
    }

}