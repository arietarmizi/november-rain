<?php

namespace api\forms\gallery;

use api\components\BaseForm;
use api\components\HttpException;
use common\models\PhotoCompetition;
use common\models\PhotoCompetitionReply;
use yii\helpers\ArrayHelper;

class CompetitionDetailForm extends BaseForm
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
        $query        = PhotoCompetition::find()->where([
            PhotoCompetition::tableName() . '.id'      => $this->id,
            PhotoCompetition::tableName() . '.eventId' => $this->eventId,
            PhotoCompetition::tableName() . '.status'  => PhotoCompetition::STATUS_ACTIVE
        ])->one();
        $this->_items = $query;
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            PhotoCompetition::class => [
                'id',
                'eventId',
                'eventName'   => function ($model) {
                    return $model->event->name;
                },
                'userId',
                'userName'    => function ($model) {
                    return $model->user->name;
                },
                'companyName' => function ($model) {
                    return $model->user->company->name;
                },
                'fileUrl'     => function ($model) {
                    if ($model->file) {
                        return $model->file->getSource();
                    }
                    return null;
                },
                'day',
                'totalReply'  => function ($model) {
                    $query = PhotoCompetitionReply::find()->where([
                        PhotoCompetitionReply::tableName() . '.status'             => PhotoCompetitionReply::STATUS_ACTIVE,
                        PhotoCompetitionReply::tableName() . '.photoCompetitionId' => $model->id,
                    ])->count();
                    return $query;
                },
                'status'      => function ($model) {
                    return PhotoCompetition::statuses()[$model->status];
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
}