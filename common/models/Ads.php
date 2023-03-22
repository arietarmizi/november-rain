<?php


namespace common\models;

use common\base\ActiveRecord;
use nadzif\file\models\File;
use yii\helpers\StringHelper;

/**
 * Class Ads
 * @package common\models
 * @property string  $id
 * @property string  $fileId
 * @property string  $subject
 * @property string  $description
 * @property string  $validFrom
 * @property string  $validUntil
 * @property string  $showTo
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property File    $file
 * @property AdsShow $show
 */
class Ads extends ActiveRecord
{
    const SEND_ALL    = 'all';
    const SEND_REGION = 'region';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%ads}}';
    }

    public static function sendto()
    {
        return [
            self::SEND_ALL    => \Yii::t('app', 'All User'),
            self::SEND_REGION => \Yii::t('app', 'Specific Region')
        ];
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'inactive')
        ];
    }

    public function getShow()
    {
        return $this->hasMany(AdsShow::class, ['adsid' => 'id']);
    }

    public function attributeLabels()
    {
        return [
            'id'          => \Yii::t('app', 'ID'),
            'fileId'      => \Yii::t('app', 'Image'),
            'subject'     => \Yii::t('app', 'Subject'),
            'description' => \Yii::t('app', 'Description'),
            'validFrom'   => \Yii::t('app', 'Valid From'),
            'validUntil'  => \Yii::t('app', 'Valid Until'),
            'showTo'      => \Yii::t('app', 'showTo'),
            'regionList'  => \Yii::t('app', 'regionList'),
            'status'      => \Yii::t('app', 'status'),
            'createdAt'   => \Yii::t('app', 'Created At'),
            'updatedAt'   => \Yii::t('app', 'Updated At')
        ];
    }

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
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
        $regex  = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg    = strip_tags($regex);
        return StringHelper::truncate($msg, $length);
    }
}