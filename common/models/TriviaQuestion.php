<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;

/**
 *
 * @property string         $id
 * @property string         $triviaId
 * @property string         $question
 * @property string         $status
 * @property string         $createdAt
 * @property string         $updatedAt
 *
 * @property Trivia         $trivia
 * @property TriviaAnswer[] $triviaAnswers
 */
class TriviaQuestion extends ActiveRecord
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

    public function getTrivia()
    {
        return $this->hasOne(Trivia::class, ['id' => 'triviaId']);
    }

    public function getTriviaAnswers()
    {
        return $this->hasMany(TriviaAnswer::class, ['triviaQuestionId' => 'id']);
    }
}