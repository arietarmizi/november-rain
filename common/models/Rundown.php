<?php

namespace common\models;

use Carbon\Carbon;
use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use Yii;

/**
 * Class Rundown
 *
 * @package common\models
 * @property string  $id
 * @property string  $eventId
 * @property string  $name
 * @property string  $description
 * @property integer $day
 * @property string  $start
 * @property string  $end
 * @property string  $pic
 * @property string  $location
 * @property string  $fileId
 * @property boolean $hasPresence
 * @property string  $isDone
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property File    $file
 * @property Event   $event
 */
class Rundown extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ISDONE_TRUE  = 'done';
    const ISDONE_FALSE = 'onprogress';

    public static function tableName()
    {
        return '{{%rundown}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public static function isdones()
    {
        return [
            self::ISDONE_TRUE  => Yii::t('app', 'Done'),
            self::ISDONE_FALSE => Yii::t('app', 'On Progress'),
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'Id'),
            'eventId'     => \Yii::t('app', 'Event Id'),
            'name'        => \Yii::t('app', 'Name'),
            'description' => \Yii::t('app', 'Description'),
            'day'         => \Yii::t('app', 'Day'),
            'start'       => \Yii::t('app', 'Start'),
            'end'         => \Yii::t('app', 'End'),
            'pic'         => \Yii::t('app', 'Pic'),
            'location'    => \Yii::t('app', 'Location'),
            'fileId'      => \Yii::t('app', 'file'),
            'hasPresence' => \Yii::t('app', 'HasPresence'),
            'isDone'      => \Yii::t('app', 'Progress'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Created At'),
            'updatedAt'   => \Yii::t('app', 'Updated At'),
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'eventId']);
    }

    public function getDate()
    {
        return Carbon::createFromTimeString($this->start)->format('Y-m-d');
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }
}
