<?php

namespace api\forms\reward;

use common\models\Reward;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use api\components\BaseForm;

class RewardForm extends BaseForm
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
        $query = Reward::find()->where([
            'eventId' => $this->eventId,
            'status'  => Reward::STATUS_ACTIVE
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
            Reward::class => [
                'id',
                'eventId',
                'eventName'   => function ($model) {
                    return $model->event->name;
                },
                'fileId',
                'fileUrl'     => function ($model) {
                    if ($model->file) {
                        return $model->file->getSource();
                    }
                    return null;
                },
                'name'        => function ($model) {
                    return Reward::generateShort($model->name);
                },
                'description' => function ($model) {
                    return Reward::generateShort($model->description);
                },
                'pointRedeem',
                'quota',
                'status',
                'createdAt',
                'updatedAt'
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