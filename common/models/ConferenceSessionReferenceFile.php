<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use Yii;

/**
 * Class ConferenceReferenceFile
 *
 * @package common\models
 * @property string $id
 * @property string $conferenceSessionReferenceId
 * @property string $fileId
 * @property string $name
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 * @property File   $file
 */
class ConferenceSessionReferenceFile extends ActiveRecord {

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName() {
        return '{{%conference_session_reference_file}}';
    }

    public static function statuses() {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public function attributeLabels() {
        return [
        ];
    }

    public function getFile() {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    public function getConferenceSessionReference() {
        return $this->hasOne(ConferenceSessionReference::class, ['id' => 'conferenceSessionReferenceId']);
    }

}
