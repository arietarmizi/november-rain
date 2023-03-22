<?php

namespace api\forms\event;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\EventQuestions;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class EventQuestionListForm extends BaseForm
{
    public $eventId;
    public $pageSize;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId'], 'required'],
            [['pageSize'], 'number'],
        ];
    }

    public function submit()
    {
        $query = EventQuestions::find()->where([
            'eventId' => $this->eventId,
            'status'  => EventQuestions::STATUS_ACTIVE
        ])->addOrderBy([EventQuestions::tableName() . '.createdAt' => \SORT_DESC]);

        $dataProvider = new ActiveDataProvider([
            'query'      => $query,
            'pagination' => [
                'pageSize' => $this->pageSize,
            ],
        ]);

        $getAll = (\Yii::$app->request->get('page') == 'all');

        if ($getAll) {
            $dataProvider->setPagination(false);
        }

        $this->_items = $dataProvider->getModels();
        $pagination   = $dataProvider->getPagination();

        $this->_meta = [
            'record' => [
                'current' => $dataProvider->getCount(),
                'total'   => $dataProvider->getTotalCount()
            ],
        ];

        if ($pagination instanceof Pagination) {
            $this->_meta['page']  = [
                'current' => $pagination->getPage() + 1,
                'total'   => $pagination->getPageCount()
            ];
            $this->_meta['links'] = $pagination->getLinks();
        }
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            EventQuestions::class => [
                'id',
                'eventId',
                'eventName' => function ($model) {
                    if ($model->eventId) {
                        return $model->event->name;
                    }
                    return null;
                },
                'userName'  => function ($model) {
                    if ($model->userId) {
                        return $model->user->name;
                    }
                    return null;
                },
                'questions',
                'status'
            ],
        ]);
        return true;
    }

    public function response()
    {
        return $this->_items;
    }

    public function meta()
    {
        return $this->_meta;
    }
}