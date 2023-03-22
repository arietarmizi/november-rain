<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/17/2020
 * Time: 6:56 AM
 */

namespace common\models;


use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;

/**
 * Class PhotoCompetition
 *
 * @package common\models
 * @property string                 $id
 * @property string                 $fileId
 * @property string                 $eventId
 * @property string                 $userId
 * @property string                 $day
 * @property string                 $status
 * @property string                 $createdAt
 * @property string                 $updatedAt
 *
 * @property File                   $file
 * @property User                   $user
 * @property Event                  $event
 *
 * @property boolean                isWon
 * @property PhotoCompetitionWinner $winner
 * @property PhotoCompetitionReply  $reply
 */
class PhotoCompetition extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%photo_competition}}';
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
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId'])->joinWith(['company']);
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'eventId']);
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    public function getWinner()
    {
        return $this->hasOne(PhotoCompetitionWinner::class, ['photoCompetitionId' => 'id']);
    }

    public function getReply()
    {
        return $this->hasMany(PhotoCompetitionReply::class, ['photoCompetitionId' => 'id']);
    }

    public function getTotalReply()
    {
        $count = PhotoCompetitionReply::find()
            ->where(['photoCompetitionId' => $this->id, 'status' => PhotoCompetitionReply::STATUS_ACTIVE])
            ->count();

        return $count;

    }

    public function getIsWon()
    {
        return $this->winner ? true : false;
    }


}