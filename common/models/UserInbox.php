<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\helpers\StringHelper;

/**
 * class UserInbox
 *
 * @package common\models
 * @property string $id
 * @property string $userId
 * @property string $category
 * @property string $title
 * @property string $shortMessage
 * @property string $message
 * @property string $isQueued
 * @property string $status
 * @property string $sendAt
 * @property string $createdAt
 * @property string $updatedAt
 *
 * */
class UserInbox extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const TYPE_MULTIPLE = 'multiple';
    const TYPE_SINGLE   = 'single';

    public $userPhoneNumber;
    public $userName;

    const STATUS_SENT     = 'sent';
    const STATUS_READ     = 'read';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DELETED  = 'deleted';
    const STATUS_QUEUED   = 'queued';

    const SHORT_MESSAGE_LENGTH = 50;

    const USER_TYPE_MECHANIC = 'mechanic';
    const USER_TYPE_OWNER    = 'owner';
    const USER_TYPE_CSV      = 'csv';

    public $file;
    public $userType;

    public static function tableName()
    {
        return '{{%user_inbox}}';
    }

    /**
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_SENT     => Yii::t('app', 'Sent'),
            self::STATUS_READ     => Yii::t('app', 'Read'),
            self::STATUS_ARCHIVED => Yii::t('app', 'Archived'),
            self::STATUS_DELETED  => Yii::t('app', 'Deleted'),
            self::STATUS_QUEUED   => Yii::t('app', 'queued'),
        ];
    }

    public static function userType()
    {
        return [
            self::TYPE_MULTIPLE => Yii::t('app', 'All User'),
            self::TYPE_SINGLE   => Yii::t('app', 'User Spesific'),
        ];
    }

    public static function generateShortMessage($string, $length = null)
    {
        $length = $length ?: self::SHORT_MESSAGE_LENGTH;

        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return StringHelper::truncate($msg, $length);
    }

    public function behaviors()
    {
        $behaviors                       = parent::behaviors();
        $behaviors['createShortMessage'] = [
            'class'      => AttributeBehavior::class,
            'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => 'shortMessage'],
            'value'      => function ($event) {
                return self::generateShortMessage($this->message, self::SHORT_MESSAGE_LENGTH);
            },
        ];

        return $behaviors;
    }

    public function attributeLabels()
    {
        return [
            'id'           => Yii::t('app', 'Id'),
            'userId'       => Yii::t('app', 'User'),
            'category'     => Yii::t('app', 'Category'),
            'title'        => Yii::t('app', 'Title'),
            'message'      => Yii::t('app', 'Message'),
            'shortMessage' => Yii::t('app', 'Short Message'),
            'status'       => Yii::t('app', 'Status'),
            'sendAt'       => Yii::t('app', 'Send At'),
            'updatedAt'    => Yii::t('app', 'Updated At'),
            'createdAt'    => Yii::t('app', 'Created At'),
            'file'         => Yii::t('app', 'Select User File'),
            'usertype'     => Yii::t('app', 'User Type'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

}
