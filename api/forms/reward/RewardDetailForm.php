<?php

namespace api\forms\reward;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\Reward;
use yii\helpers\ArrayHelper;

class RewardDetailForm extends BaseForm
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
        $query        = Reward::find()->where([
            Reward::tableName() . '.id'      => $this->id,
            Reward::tableName() . '.eventId' => $this->eventId,
            Reward::tableName() . '.status'  => Reward::STATUS_ACTIVE
        ])->one();
        $this->_items = $query;
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
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
                    return $model->name;
                },
                'description' => function ($model) {
                    return $model->description;
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
}