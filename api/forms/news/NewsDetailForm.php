<?php

namespace api\forms\news;

use api\components\HttpException;
use common\models\News;
use yii\helpers\ArrayHelper;
use api\components\BaseForm;

class NewsDetailForm extends BaseForm
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
        $query        = News::find()->where([
            News::tableName() . '.id'      => $this->id,
            News::tableName() . '.eventId' => $this->eventId,
            News::tableName() . '.status'  => News::STATUS_ACTIVE
        ])->one();
        $this->_items = $query;
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            News::class => [
                'eventId',
                'eventName' => function ($model) {
                    if ($model->file) {
                        return $model->event->name;
                    }
                    return null;
                },
                'fileId',
                'fileUrl'   => function ($model) {
                    if ($model->file) {
                        return $model->file->getSource();
                    }
                    return null;
                },
                'category',
                'title',
                'description',
                'status'    => function ($model) {
                    return News::statuses()[$model->status];
                },
                'createdAt' => function ($model) {
                    return \Yii::$app->formatter->asDate($model->createdAt);
                },
                'updatedAt' => function ($model) {
                    return \Yii::$app->formatter->asDate($model->updatedAt);
                }
            ],
        ]);
        return true;
    }

    public function response()
    {
        return $this->_items;
    }
}