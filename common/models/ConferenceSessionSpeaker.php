<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/12/2020
 * Time: 5:30 AM
 */

namespace common\models;


use nadzif\base\models\ActiveRecord;

/**
 * Class ConferenceSessionSpeaker
 *
 * @package common\models
 * @property string            $conferenceSessionId
 * @property string            $speakerId
 * @property string            $status
 * @property string            $createdAt
 * @property string            $updatedAt
 *
 * @property ConferenceSession $conferenceSession
 * @property Speaker           $speaker
 */
class ConferenceSessionSpeaker extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%conference_session_speaker}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public function getSpeaker()
    {
        return $this->hasOne(Speaker::class, ['id' => 'speakerId']);
    }

    public function getConferenceSession()
    {
        return $this->hasOne(ConferenceSession::class, ['id' => 'conferenceSessionId']);
    }
}