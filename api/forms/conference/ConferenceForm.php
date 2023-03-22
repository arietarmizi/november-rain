<?php

namespace api\forms\conference;

use api\components\BaseForm;
use common\models\Conference;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class ConferenceForm extends BaseForm
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
        $query = Conference::find()->where([
            'eventId' => $this->eventId,
            'status'  => Conference::STATUS_ACTIVE
        ])->andWhere([
            'and',
            ['<=', Conference::tableName() . '.start', \Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s')],
            ['>=', Conference::tableName() . '.end', \Yii::$app->formatter->asDatetime('now', 'php:Y-m-d H:i:s')],
        ])->addOrderBy(['createdAt' => \SORT_DESC]);

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
        $this->_items = ArrayHelper::toArray($this->_items, [
            Conference::class => [
                'id',
                'eventId',
                'eventName' => function ($model) {
                    return $model->event->name;
                },
                'name',
                'description',
                'start',
                'end',
                'location',
                'latitude',
                'longitude',
                'status'    => function ($model) {
                    return Conference::statuses()[$model->status];
                },
                'createdAt',
                'updatedAt'
            ]
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