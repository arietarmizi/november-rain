<?php

namespace api\forms\gallery;

use api\components\BaseForm;
use common\models\PhotoCompetition;
use yii\data\ActiveDataProvider;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;

class CompetitionDayListForm extends BaseForm
{
    public $eventId;
    public $pageSize;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId'], 'required'],
            [['pageSize'], 'number']
        ];
    }

    public function submit()
    {
        $query        = PhotoCompetition::find()->where([
            PhotoCompetition::tableName() . '.eventId' => $this->eventId,
            PhotoCompetition::tableName() . '.status'  => PhotoCompetition::STATUS_ACTIVE,
        ])->groupBy([PhotoCompetition::tableName() . '.day'])->addOrderBy([PhotoCompetition::tableName() . '.day' => \SORT_ASC]);
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
                'day'
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