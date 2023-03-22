<?php

namespace api\forms\schedule;

use api\components\BaseForm;
use common\models\Rundown;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

class ScheduleListForm extends BaseForm
{
    public $eventId;
    public $day;
    public $_items;
    public $_meta;

    public function rules()
    {
        return [
            [['eventId'], 'required'],
            [['day'], 'string'],
        ];
    }

    public function submit()
    {
        $query = Rundown::find()
            ->where([
                'eventId' => $this->eventId,
                'status'  => Rundown::STATUS_ACTIVE
            ]);
        if ($this->day) {
            $query->andWhere(['day' => $this->day]);
        }
        $query->addOrderBy(['day' => \SORT_ASC, 'start' => SORT_ASC]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query
        ]);
        $this->_items = $dataProvider->getModels();
        $this->_items = ArrayHelper::toArray($this->_items, [
            Rundown::class => [
                'day',
                'name',
                'description',
                'start',
                'end',
                'location',
                'pic',
                'hasPresence',
                'isDone' => function ($model) {
                    /** @var Rundown $model */
                    return Rundown::isdones()[$model->isDone];
                },
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