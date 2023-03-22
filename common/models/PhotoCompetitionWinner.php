<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/17/2020
 * Time: 7:05 AM
 */

namespace common\models;


use nadzif\base\models\ActiveRecord;

/**
 * Class PhotoCompetitionWinner
 *
 * @package common\models
 * @property string  $id
 * @property string  $photoCompetitionId
 * @property string  $activityQrCodeId
 * @property string  $group
 * @property string  $place
 * @property integer $sequence
 * @property double  $pointReceived
 * @property string  $status
 * @property string  $createdAt
 * @property string  $updatedAt
 *
 * @property PhotoCompetition $photoCompetition
 */
class PhotoCompetitionWinner extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%photo_competition_winner}}';
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
        ];
    }

    public function getPhotoCompetition()
    {
        return $this->hasOne(PhotoCompetition::class, ['id' => 'photoCompetitionId'])
            ->joinWith(['file', 'user']);
    }

}