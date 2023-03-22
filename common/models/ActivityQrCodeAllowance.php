<?php
/**
 * Created by PhpStorm.
 * User: Nadzif Glovory
 * Date: 2/12/2020
 * Time: 5:31 AM
 */

namespace common\models;


use nadzif\base\models\ActiveRecord;

/**
 * Class ActivityQrCodeAllowance
 *
 * @package common\models
 * @property string $id
 * @property string $activityQrCodeId
 * @property string $userType
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property ActivityQrCode 4activityQrCode
 */
class ActivityQrCodeAllowance extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName()
    {
        return '{{%activity_qr_code_allowance}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function userTypes()
    {
        return User::types();
    }

    public function getActivityQrCode()
    {
        return $this->hasOne(ActivityQrCode::class, ['id' => 'activityQrCodeId']);
    }
}