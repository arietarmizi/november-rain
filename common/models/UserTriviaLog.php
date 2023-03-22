<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;

/**
 *
 * @property string $id
 * @property string $userId
 * @property string $triviaId
 * @property string $action
 * @property double $score
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 *
 * @property User   $user
 * @property Trivia $trivia
 */
class UserTriviaLog extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    const ACTION_FOUND = 'found';
    const ACTION_DONE  = 'done';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public static function actions()
    {
        return [
            self::ACTION_FOUND => \Yii::t('app', 'Found'),
            self::ACTION_DONE  => \Yii::t('app', 'Done'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function getTrivia()
    {
        return $this->hasOne(Trivia::class, ['id' => 'triviaId']);
    }
}