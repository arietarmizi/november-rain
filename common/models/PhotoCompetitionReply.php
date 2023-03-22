<?php


namespace common\models;

use common\models\Gallery;
use common\models\User;
use nadzif\base\models\ActiveRecord;

/**
 * Class PhotoCompetitionReply
 * @package common\models
 *
 * @property string           $id
 * @property string           $photoCompetitionId
 * @property string           $userId
 * @property string           $reply
 * @property string           $status
 * @property string           $createdAt
 * @property string           $updatedAt
 *
 * @property PhotoCompetition $photoCompetition
 * @property User             $user
 */
class PhotoCompetitionReply extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';
    const STATUS_ARCHIVED = 'archived';
    const STATUS_DELETED  = 'deleted';

    public static function tableName()
    {
        return '{{%photo_competition_reply}}';
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
            'galleryId' => \Yii::t('app', 'Gallery'),
            'userId'    => \Yii::t('app', 'User'),
            'reply'     => \Yii::t('app', 'Reply'),
            'status'    => \Yii::t('app', 'Status'),
            'createdAt' => \Yii::t('app', 'Created At'),
            'updatedAt' => \Yii::t('app', 'Updated At'),
        ];
    }

    public function getPhotoCompetition()
    {
        return $this->hasOne(PhotoCompetition::class, ['id' => 'photoCompetitionId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

}