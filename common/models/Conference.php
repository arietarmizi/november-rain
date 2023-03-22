<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;
use yii\helpers\StringHelper;

/**
 * Class Conference
 *
 * @package common\models
 * @property string              $id
 * @property string              $eventId
 * @property string              $name
 * @property string              $description
 * @property string              $date
 * @property string              $start
 * @property string              $end
 * @property string              $location
 * @property double              $latitude
 * @property double              $longitude
 * @property string              $status
 * @property string              $createdAt
 * @property string              $updatedAt
 *
 * @property ConferenceSession[] $conferenceSessions
 * @property Event               $event
 *
 *
 */
class Conference extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%conference}}';
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
            'id'          => \Yii::t('app', 'Id'),
            'name'        => \Yii::t('app', 'Name'),
            'description' => \Yii::t('app', 'Description'),
            'start'       => \Yii::t('app', 'Start'),
            'end'         => \Yii::t('app', 'End'),
            'latitude'    => \Yii::t('app', 'Latitude'),
            'longitude'   => \Yii::t('app', 'Longitude'),
            'status'      => \Yii::t('app', 'Status'),
            'createdAt'   => \Yii::t('app', 'Created At'),
            'updatedAt'   => \Yii::t('app', 'Updated At'),
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'eventId']);
    }

    public function getConferenceSessions()
    {
        return $this->hasMany(ConferenceSession::class, ['conferenceId' => 'id'])
            ->addOrderBy([ConferenceSession::tableName() . '.name'=> SORT_ASC]);
    }
    public static function descriptions($string)
    {
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return $msg;
    }
    public static function generateShort($string, $length = null)
    {
        $length = $length ?: 50;
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return StringHelper::truncate($msg, $length);
    }

}
