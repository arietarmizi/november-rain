<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;
use Yii;

/**
 * Class UserPoint
 *
 * @package common\models
 * @property string   $id
 * @property string   $userId
 * @property string   $activityId
 * @property string   $code
 * @property double   $point
 * @property string   $reference
 * @property string   $refId
 * @property string   $status
 * @property string   $createdAt
 * @property string   $updatedAt
 *
 * @property Activity $activity
 * @property User     $user
 *
 */
class UserPoint extends ActiveRecord
{

    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const REFERENCE_EVENT         = 'event';
    const REFERENCE_TRIVIA_ANSWER = 'triviaAnswer';
    const REFERENCE_TRIVIA_BONUS  = 'triviaBonus';
    const REFERENCE_TRIVIA_FOUND  = 'triviaFound';

    public $name;
    public $totalPoint;
    public $companyName;

    public static function tableName()
    {
        return '{{%user_point}}';
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
            'eventName'    => Yii::t('app', 'Event'),
            'activityName' => Yii::t('app', 'Activity'),
            'rundownName'  => Yii::t('app', 'Rundown'),
        ];
    }

    public function getActivity()
    {
        return $this->hasOne(Activity::class, ['id' => 'activityId'])->joinWith(['rundown']);
    }

    public function geUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

}
