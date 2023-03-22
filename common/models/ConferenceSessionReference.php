<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;

/**
 * Class ConferenceSessionReference
 *
 * @package common\models
 * @property string                           $id
 * @property string                           $conferenceSessionId
 * @property string                           $name
 * @property string                           $description
 * @property double                           $order
 * @property string                           $status
 * @property string                           $createdAt
 * @property string                           $updatedAt
 *
 * @property ConferenceSession                $conferenceSession
 *
 * @property ConferenceSessionReferenceFile[] $conferenceSessionReferenceFiles
 */
class ConferenceSessionReference extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%conference_session_reference}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
        ];
    }

    public function getConferenceSession()
    {
        return $this->hasOne(Conference::class, ['id' => 'conferenceSessionId']);
    }

    public function getConferenceSessionReferenceFiles()
    {
        return $this->hasMany(ConferenceSessionReferenceFile::class, ['conferenceSessionReferenceId' => 'id']);
    }

}
