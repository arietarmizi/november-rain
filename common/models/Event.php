<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use nadzif\file\models\File;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\StringHelper;

/**
 * Class Event
 *
 * @package common\models
 * @property string    $id
 * @property string    $fileId
 * @property string    $name
 * @property string    $description
 * @property string    $announcement
 * @property string    $start
 * @property string    $end
 * @property double    $latitude
 * @property double    $longitude
 * @property string    $status
 * @property string    $createdAt
 * @property string    $updatedAt
 *
 * @property File      $file
 *
 * @property Rundown[] $rundowns
 * @property Gallery[] $galleries
 *
 */
class Event extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%event}}';
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

    public function getFile()
    {
        return $this->hasOne(File::class, ['id' => 'fileId']);
    }

    public function getParticipants()
    {
        return $this->hasMany(Participant::class, ['eventId' => 'id']);
    }

    public function getTotalParticipants()
    {
        return $this->hasMany(Participant::class, ['eventId' => 'id'])->count();
    }

    public function getRundowns()
    {
        return $this->hasMany(Rundown::class, ['eventId' => 'id'])
            ->addOrderBy(['day' => SORT_ASC, 'start' => SORT_ASC]);
    }

    public function getGalleries()
    {
        return $this->hasMany(Gallery::class, ['eventId' => 'id'])
            ->andWhere([Gallery::tableName() . '.status' => Gallery::STATUS_ACTIVE]);
    }

    public function sortDay()
    {
        $this->refresh();

        $dateSorter = ArrayHelper::map($this->rundowns, 'id', 'day', 'date');
        ksort($dateSorter);


        $iC = 1;
        foreach ($dateSorter as $date => $data) {
            Rundown::updateAll(['day' => $iC], ['in', 'id', array_keys($data)]);
            $iC++;
        }

        return true;
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
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $string));
        $msg   = strip_tags($regex);
        return StringHelper::truncate($msg, $length);
    }

}
