<?php

namespace common\models;

use common\base\ActiveRecord;

/**
 *
 * @property string         $id
 * @property string         $userId
 * @property string         $triviaQuestionId
 * @property string         $triviaAnswerId
 * @property double         $score
 * @property string         $status
 * @property string         $createdAt
 * @property string         $updatedAt
 *
 * @property User           $user
 * @property TriviaQuestion $triviaQuestion
 * @property TriviaAnswer   $triviaAnswer
 */
class UserTriviaAnswer extends ActiveRecord
{
    const STATUS_ACTIVE   = 'active';
    const STATUS_INACTIVE = 'inactive';

    public static function statuses()
    {
        return [
            self::STATUS_ACTIVE   => \Yii::t('app', 'Active'),
            self::STATUS_INACTIVE => \Yii::t('app', 'Inactive'),
        ];
    }

    public function getUser()
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }

    public function getTriviaQuestion()
    {
        return $this->hasOne(TriviaQuestion::class, ['id' => 'triviaQuestionId']);
    }

    public function getTriviaAnswer()
    {
        return $this->hasOne(TriviaAnswer::class, ['id' => 'triviaAnswerId']);
    }
}