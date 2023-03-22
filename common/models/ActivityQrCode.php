<?php

namespace common\models;

use common\helpers\Coupon;
use nadzif\base\models\ActiveRecord;
use Yii;
use yii\behaviors\AttributeBehavior;

/**
 * Class ActivityQrCode
 *
 * @package common\models
 * @property string                    $id
 * @property string                    $activityId
 * @property string                    $code
 * @property double                    $quota
 * @property double                    $claimed
 * @property double                    $point
 * @property double                    $maximumScan
 * @property boolean                   $uniqueUser
 * @property string                    $status
 * @property string                    $createdAt
 * @property string                    $updatedAt
 *
 * @property Activity                  $activity
 * @property ActivityQrCodeAllowance[] $activityQrCodeAllowances
 *
 */
class ActivityQrCode extends ActiveRecord
{

    const UNIQUEUSER_TRUE  = '1';
    const UNIQUEUSER_FALSE = '0';

    const STATUS_AVAILABLE = 'available';
    const STATUS_EXCEEDED  = 'exceeded';
    const STATUS_INACTIVE  = 'inactive';

    public static function tableName()
    {
        return '{{%activity_qr_code}}';
    }

    public static function statuses()
    {
        return [
            self::STATUS_AVAILABLE => Yii::t('app', 'Available'),
            self::STATUS_EXCEEDED  => Yii::t('app', 'Exceeded'),
            self::STATUS_INACTIVE  => Yii::t('app', 'Inactive'),
        ];
    }

    public static function uniques()
    {
        return [
            self::UNIQUEUSER_TRUE  => Yii::t('app', 'True'),
            self::UNIQUEUSER_FALSE => Yii::t('app', 'False'),
        ];
    }

    public function behaviors()
    {
        $behaviors   = parent::behaviors();
        $behaviors[] = [
            'class'      => AttributeBehavior::class,
            'attributes' => [ActiveRecord::EVENT_BEFORE_INSERT => 'code'],
            'value'      => function ($event) {
                return Coupon::generate(8);
            },
        ];

        return $behaviors;
    }

    public function attributeLabels()
    {
        return [];
    }

    public function getActivity()
    {
        return $this->hasOne(Activity::class, ['id' => 'activityId'])->joinWith(['rundown']);
    }

    public function getActivityQrCodeAllowances()
    {
        return $this->hasMany(ActivityQrCodeAllowance::class, ['activityQrCodeId' => 'id']);
    }

}
