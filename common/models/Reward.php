<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use Yii;
use yii\helpers\StringHelper;

/**
 * Class Reward
 *
 * @package common\models
 * @property string $id
 * @property string $eventId
 * @property string $fileId
 * @property string $name
 * @property string $description
 * @property double $pointRedeem
 * @property double $quota
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property File   $file
 * @property Event  $event
 *
 */
class Reward extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%reward}}';
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
    public static function getDescription($string)
    {
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return $msg;
    }

    public static function generateShort($string, $length = null)
    {
        $length = $length ?: 50;
        $regex  = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg    = strip_tags($regex);
        return StringHelper::truncate($msg, $length);
    }
}
