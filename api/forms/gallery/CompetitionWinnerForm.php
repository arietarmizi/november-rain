<?php

namespace api\forms\gallery;

use api\components\BaseForm;
use common\models\PhotoCompetition;
use common\models\PhotoCompetitionReply;
use common\models\PhotoCompetitionWinner;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class CompetitionWinnerForm extends BaseForm
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
        $query = PhotoCompetitionWinner::find()->where([
            PhotoCompetitionWinner::tableName() . '.status' => PhotoCompetitionWinner::STATUS_ACTIVE,
            PhotoCompetition::tableName() . '.eventId'      => $this->eventId,
            PhotoCompetition::tableName() . '.status'       => PhotoCompetition::STATUS_ACTIVE,
        ])->joinWith(['photoCompetition']);
        if ($this->day) {
            $query->where([PhotoCompetition::tableName() . '.day' => $this->day]);
        }
        $query->orderBy([
            PhotoCompetitionWinner::tableName() . '.sequence' => \SORT_ASC,
            PhotoCompetition::tableName() . '.createdAt'      => \SORT_DESC,
        ]);
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
            PhotoCompetitionWinner::class => [
                'id',
                'day'         => function ($model) {
                    return $model->photoCompetition->day;
                },
                'group',
                'place',
                'sequence',
                'pointReceived',
                'eventName'   => function ($model) {
                    return $model->photoCompetition->event->name;
                },
                'userName'    => function ($model) {
                    return $model->photoCompetition->user->name;
                },
                'companyName' => function ($model) {
                    return $model->photoCompetition->user->company->name;
                },
                'fileUrl'     => function ($model) {
                    return isset($model->photoCompetition->file) ? $model->photoCompetition->file->getSource() : null;
                },
                'totalReply'  => function ($model) {
                    $query = PhotoCompetitionReply::find()->where([
                        PhotoCompetitionReply::tableName() . '.status'             => PhotoCompetitionReply::STATUS_ACTIVE,
                        PhotoCompetitionReply::tableName() . '.photoCompetitionId' => $model->photoCompetitionId,
                    ])->count();
                    return $query;
                },
                'createdAt'   => function ($model) {
                    return $model->photoCompetition->createdAt;
                },
                'updatedAt'   => function ($model) {
                    return $model->photoCompetition->updatedAt;
                }
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