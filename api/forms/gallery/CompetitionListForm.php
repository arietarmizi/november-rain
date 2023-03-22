<?php

namespace api\forms\gallery;

use api\components\BaseForm;
use common\models\PhotoCompetition;
use common\models\PhotoCompetitionReply;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class CompetitionListForm extends BaseForm
{
    public $eventId;
    public $pageSize;
    public $day;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId'], 'required'],
            [['pageSize'], 'number'],
            [['day'], 'string'],
        ];
    }

    public function submit()
    {
        $query = PhotoCompetition::find()->where([
            PhotoCompetition::tableName() . '.eventId' => $this->eventId,
            PhotoCompetition::tableName() . '.status'  => PhotoCompetition::STATUS_ACTIVE,
        ]);
        if ($this->day) {
            $query->where([PhotoCompetition::tableName() . '.day' => $this->day]);
        }
        $query->addOrderBy([PhotoCompetition::tableName() . '.createdAt' => \SORT_DESC]);
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
            PhotoCompetition::class => [
                'id',
                'day',
                'eventId',
                'eventName'   => function ($model) {
                    return isset($model->event) ? $model->event->name : null;
                },
                'userName'    => function ($model) {
                    return isset($model->user) ? $model->user->name : null;
                },
                'companyName' => function ($model) {
                    return isset($model->user->company) ? $model->user->company->name : null;
                },
                'fileUrl'     => function ($model) {
                    return isset($model->file) ? $model->file->getSource() : null;
                },
                'totalReply'  => function ($model) {
                    $query = PhotoCompetitionReply::find()->where([
                        PhotoCompetitionReply::tableName() . '.status'             => PhotoCompetitionReply::STATUS_ACTIVE,
                        PhotoCompetitionReply::tableName() . '.photoCompetitionId' => $model->id,
                    ])->count();
                    return $query;
                },
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