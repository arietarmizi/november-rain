<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/12/2020
 * Time: 5:30 AM
 */

namespace common\models;


use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use yii\helpers\StringHelper;

/**
 * Class News
 *
 * @package common\models
 * @property string $id
 * @property string $eventId
 * @property string $fileId
 * @property string $category
 * @property string $title
 * @property string $description
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property File   $file
 * @property Event  $event
 */
class News extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const CATEGORY_GENERAL = 'general';
    const CATEGORY_EVENT   = 'event';

    public static function tableName()
    {
        return '{{%news}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function categories()
    {
        return [
            self::CATEGORY_GENERAL => \Yii::t('app', 'Project'),
            self::CATEGORY_EVENT   => \Yii::t('app', 'Event'),
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