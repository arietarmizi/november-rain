<?php

namespace api\forms\event;

use common\models\EventQuestions;
use yii\helpers\ArrayHelper;
use api\components\HttpException;
use api\components\BaseForm;

class EventQuestionDetailForm extends BaseForm
{
    public $eventId;
    public $id;
    public $pageSize;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId', 'id'], 'required']
        ];
    }

    public function submit()
    {
        $query        = EventQuestions::find()->where([
            EventQuestions::tableName() . '.id'      => $this->id,
            EventQuestions::tableName() . '.eventId' => $this->eventId,
            EventQuestions::tableName() . '.status'  => EventQuestions::STATUS_ACTIVE
        ])->one();
        $this->_items = $query;
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            EventQuestions::class => [
                'id',
                'eventId',
                'eventName' => function ($model) {
                    return $model->event->name;
                },
                'userName'  => function ($model) {
                    if ($model->userId) {
                        return $model->user->name;
                    }
                    return null;
                },
                'questions' => function ($model) {
                    return $model->questions;
                },
                'status',
                'createdAt',
                'updatedAt',
            ],
        ]);
        return true;
    }

    public function response()
    {
        return $this->_items;
    }
}