<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;

/**
 * Class Redeem
 * @package common\models
 * @property string $id
 * @property string $userId
 * @property string $rewardId
 * @property double $pointExist
 * @property double $pointSpent
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 */
class Redeem extends ActiveRecord {

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function tableName() {
        return '{{%redeem}}';
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

}
