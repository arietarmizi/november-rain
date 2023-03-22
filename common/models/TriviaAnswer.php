<?php

namespace common\models;


use nadzif\base\models\ActiveRecord;

/**
 *
 * @property string         $id
 * @property string         $triviaQuestionId
 * @property string         $answer
 * @property double         $score
 * @property string         $status
 * @property string         $createdAt
 * @property string         $updatedAt
 *
 * @property TriviaQuestion $triviaQuestion
 */
class TriviaAnswer extends ActiveRecord
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

    public function getTriviaQuestion()
    {
        return $this->hasOne(TriviaQuestion::class, ['id' => 'triviaQuestionId']);
    }
}