<?php


namespace common\models;

use common\base\ActiveRecord;
use nadzif\file\models\File;

/**
 * Class AdsShow
 * @package common\models
 * @property string $id
 * @property string $adsid
 * @property string $regionid
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property File   $file
 */
class AdsShow extends ActiveRecord
{
    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%ads_show}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'inactive')
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'        => \Yii::t('app', 'ID'),
            'adsId'     => \Yii::t('app', 'adsId'),
            'status'    => \Yii::t('app', 'status'),
            'createdAt' => \Yii::t('app', 'Created At'),
            'updatedAt' => \Yii::t('app', 'Updated At')
        ];
    }
}