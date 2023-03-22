<?php

namespace api\forms\schedule;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\Rundown;
use yii\helpers\ArrayHelper;

class ScheduleDetailForm extends BaseForm
{
    public $eventId;
    public $id;
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
        $query        = Rundown::find()
            ->select([
                Rundown::tableName() . '.eventId',
                Rundown::tableName() . '.day',
            ])->where([
                Rundown::tableName() . '.id'      => $this->id,
                Rundown::tableName() . '.eventId' => $this->eventId,
                Rundown::tableName() . '.status'  => Rundown::STATUS_ACTIVE
            ])->groupBy([
                Rundown::tableName() . '.eventId',
            ])->orderBy(['day' => SORT_ASC, 'start' => SORT_ASC])->one();
        $this->_items = $query;
        if(!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            Rundown::class => [
                'id',
                'eventId',
                'eventName' => function($model) {
                    return $model->event->name;
                },
                'name',
                'description',
                'day',
                'start'     => function($model) {
                    if($expiredAt = ArrayHelper::getValue($model, 'start')) {
                        return \Yii::$app->formatter->asDatetime($expiredAt);
                    }
                },
                'end'       => function($model) {
                    if($expiredAt = ArrayHelper::getValue($model, 'end')) {
                        return \Yii::$app->formatter->asDatetime($expiredAt);
                    }
                },
                'pic',
                'location',
                'fileId',
                'fileUrl'   => function($model) {
                    if($model->file) {
                        return $model->file->getSource();
                    }
                    return null;
                },
                'isDone'    => function($model) {
                    /** @var Rundown $model */
                    return Rundown::isdone()[$model->isDone];
                },
                'hasPresence',
                'status'
            ],
        ]);
        return true;
    }

    public function response()
    {
        return $this->_items;
    }
}