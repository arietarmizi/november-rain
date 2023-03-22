<?php

namespace api\forms\gallery;

use Carbon\Carbon;
use common\models\Conference;
use common\models\ConferenceSession;
use common\models\ConferenceSessionReference;
use common\models\ConferenceSessionReferenceFile;
use common\models\PhotoCompetition;
use common\models\PhotoCompetitionReply;
use common\models\PhotoCompetitionWinner;
use common\models\Speaker;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\data\ActiveDataProvider;
use api\components\HttpException;
use api\components\BaseForm;

class CompetitionWinnerDetailForm extends BaseForm
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
        $query = PhotoCompetitionWinner::find()->where([
            PhotoCompetitionWinner::tableName() . '.id'     => $this->id,
            PhotoCompetition::tableName() . '.eventId'      => $this->eventId,
            PhotoCompetition::tableName() . '.status'       => PhotoCompetition::STATUS_ACTIVE,
            PhotoCompetitionWinner::tableName() . '.status' => PhotoCompetitionWinner::STATUS_ACTIVE
        ])->joinWith(['photoCompetition'])->one();
        $this->_items = $query;
        if (!$this->_items) {
            throw new HttpException(400, \Yii::t('app', 'Data not found'));
        }
        $this->_items = ArrayHelper::toArray($this->_items, [
            PhotoCompetitionWinner::class => [
                'id',
                'photoCompetitionId',
                'photoCompetitionUserName' => function ($model) {
                    return $model->photoCompetition->user->name;
                },
                'activityQrCodeId',
                'group',
                'place',
                'sequence',
                'pointReceived',
                'companyName' => function ($model) {
                    return $model->photoCompetition->user->company->name;
                },
                'fileUrl'     => function ($model) {
                    if ($model->photoCompetition->file) {
                        return $model->photoCompetition->file->getSource();
                    }
                    return null;
                },
                'totalReply'  => function ($model) {
                    $query = PhotoCompetitionReply::find()->where([
                        PhotoCompetitionReply::tableName() . '.status'             => PhotoCompetitionReply::STATUS_ACTIVE,
                        PhotoCompetitionReply::tableName() . '.photoCompetitionId' => $model->photoCompetitionId,
                    ])->count();
                    return $query;
                },
                'status'               => function ($model) {
                    return PhotoCompetitionWinner::statuses()[$model->status];
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