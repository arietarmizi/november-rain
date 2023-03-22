<?php

namespace common\models;

use nadzif\base\models\ActiveRecord;

/**
 * Class Faq
 * @package common\models
 * @property string $id
 * @property string $sequence
 * @property string $question
 * @property string $answer
 * @property string $status
 * @property string $createdAt
 * @property string $updatedAt
 */
class Faq extends ActiveRecord
{

    const STATUS_ACTIVE            = 'active';
    const STATUS_INACTIVE          = 'inactive';
    const SHORT_DESCRIPTION_LENGTH = 50;

    public static function tableName()
    {
        return '{{%faq}}';
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
            'id'        => \Yii::t('app', 'ID'),
            'sequence'  => \Yii::t('app', 'Sequence'),
            'question'  => \Yii::t('app', 'Question'),
            'answer'    => \Yii::t('app', 'Answer'),
            'status'    => \Yii::t('app', 'Status'),
            'createdAt' => \Yii::t('app', 'Created At'),
            'updatedAt' => \Yii::t('app', 'Updated At'),
        ];
    }

    public function getShortQuestion()
    {
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $this->question));
        $desc  = strip_tags($regex);
        return \yii\helpers\StringHelper::truncate($desc, self::SHORT_DESCRIPTION_LENGTH);
    }

    public function getShortAnswer()
    {
        $regex = preg_replace('/&#?[a-z0-9]+;/i', '', preg_replace('/\r|\n/', '', $this->answer));
        $desc  = strip_tags($regex);
        return \yii\helpers\StringHelper::truncate($desc, self::SHORT_DESCRIPTION_LENGTH);
    }
}
