<?php

namespace common\models;


use nadzif\base\models\ActiveRecord;

/**
 * Class ConferenceSession
 *
 * @package common\models
 * @property string                     $id
 * @property string                     $conferenceId
 * @property string                     $name
 * @property string                     $speakerId
 * @property string                     $description
 * @property string                     $status
 * @property string                     $createdAt
 * @property string                     $updatedAt
 *
 * @property Conference                 $conference
 * @property Speaker                    $speaker
 * @property Speaker                    $activeSpeaker
 * @property ConferenceSessionReference $conferenceSessionReferences
 *
 */
class ConferenceSession extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%conference_session}}';
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
            'speakerId' => \Yii::t('app', 'Speaker')
        ];
    }

    public function getConference()
    {
        return $this->hasOne(Conference::class, ['id' => 'conferenceId']);
    }

    public function getSpeaker()
    {
        return $this->hasOne(Speaker::class, ['id' => 'speakerId']);
    }


    public function getConferenceSessionReferences()
    {
        return $this->hasMany(ConferenceSessionReference::class, ['conferenceSessionId' => 'id']);
    }

}