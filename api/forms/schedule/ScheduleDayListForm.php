<?php

namespace api\forms\schedule;

use api\components\BaseForm;
use common\models\Rundown;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ScheduleDayListForm extends BaseForm
{
    public $eventId;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId'], 'required']
        ];
    }

    public function submit()
    {
        $query        = Rundown::find()->where([
            'eventId' => $this->eventId,
            'status'  => Rundown::STATUS_ACTIVE
        ])->groupBy(['day'])->addOrderBy(['day' => \SORT_ASC]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $this->_items = $dataProvider->getModels();
        $this->_items = ArrayHelper::toArray($this->_items, [
            Rundown::class => [
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