<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use Yii;

/**
 * Class Gallery
 *
 * @package common\models
 * @property string  $id
 * @property string  $fileId
 * @property string  $eventId
 * @property integer $day
 * @property string  $category
 * @property string  $title
 * @property string  $content
 * @property double  $pointReceived
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property Event   $event
 * @property File    $file
 *
 */
class Gallery extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const CATEGORY_WINNER  = 'winner';
    const CATEGORY_GENERAL = 'general';

    public static function tableName()
    {
        return '{{%gallery}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => Yii::t('app', 'Inactive'),
        ];
    }

    public static function categories()
    {
        return [
            self::CATEGORY_WINNER  => Yii::t('app', 'Winner'),
            self::CATEGORY_GENERAL => Yii::t('app', 'Project'),
        ];
    }

    public function attributeLabels()
    {
        return [
        ];
    }

    public function getEvent()
    {
        return $this->hasOne(Event::class, ['id' => 'eventId']);
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

}
